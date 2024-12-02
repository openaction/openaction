<?php

namespace App\Website\ImportExport\Parser;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Community\ContentImport;
use App\Entity\Community\Model\ContentImportSettings;
use App\Entity\Project;
use App\Entity\Website\PageCategory;
use App\Entity\Website\Post;
use App\Entity\Website\PostCategory;
use App\Repository\Platform\JobRepository;
use App\Util\Uid;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

use function Symfony\Component\String\u;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WordpressContentParser implements ExternalContentParserInterface
{
    private const TYPE_PAGE = 'page';
    private const TYPE_POST = 'post';
    private const TYPE_ATTACHMENT = 'attachment';

    private Connection $db;

    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $em,
        private readonly JobRepository $jobRepository,
        private readonly CdnUploader $cdnUploader,
    ) {
    }

    public function getSupportedSource(): string
    {
        return ContentImportSettings::IMPORT_SOURCE_WORDPRESS;
    }

    public function import(ContentImport $import, string $filename): void
    {
        $this->db = $this->em->getConnection();

        $jobId = $import->getJob()->getId();

        // Parse XML data into raw arrays
        $rawData = $this->prepareRawDataFromXmlFile($import, $filename);

        // Create categories registry
        $categoriesRegistry = ['posts' => [], 'pages' => []];
        if (ContentImportSettings::KEEP_CATEGORIES_YES === $import->getSettings()['keepCategories'] ?? null) {
            $categoriesRegistry = $this->createCategoriesRegistry($import, $rawData);
        }

        // Create authors registry
        $postsAuthorsRegistry = [];
        if (!empty($import->getSettings()['postAuthorsIds'])) {
            $postsAuthorsRegistry = array_map('trim', explode(',', $import->getSettings()['postAuthorsIds']));
        }

        // Update the progress as we are about to start the import
        $step = 0;
        $total = count($rawData['posts']) + count($rawData['pages']);
        $this->jobRepository->setJobStatus($jobId, step: $step, total: $total);

        foreach ($rawData['posts'] as $postData) {
            $this->importPost($import, $postsAuthorsRegistry, $categoriesRegistry['posts'], $postData);

            ++$step;
            $this->jobRepository->setJobStatus($jobId, step: $step, total: $total);
        }

        foreach ($rawData['pages'] as $pageData) {
            $this->importPage($import, $categoriesRegistry['pages'], $pageData);

            ++$step;
            $this->jobRepository->setJobStatus($jobId, step: $step, total: $total);
        }

        $this->jobRepository->finishJob($jobId);
    }

    private function prepareRawDataFromXmlFile(ContentImport $import, string $filename): array
    {
        $postsData = [];
        $pagesData = [];
        $attachmentsData = [];

        // Parse XML
        $reader = \XMLReader::open($filename);
        while ($reader->read()) {
            if (\XMLReader::ELEMENT !== $reader->nodeType || 'item' !== $reader->localName) {
                continue;
            }

            // Parse XML data
            $entryXml = $reader->readOuterXml();

            $itemId = $this->extractTagByName($entryXml, 'wp:post_id')[0];
            $itemType = $this->extractTagByName($entryXml, 'wp:post_type')[0];
            $itemTitle = $this->extractTagByName($entryXml, 'title')[0];
            $itemDescription = $this->extractTagByName($entryXml, 'description')[0];
            $itemStatus = $this->extractTagByName($entryXml, 'wp:status')[0];

            $itemContent = $this->extractTagByName($entryXml, 'content:encoded', '')[0];
            if ($itemContent) {
                $itemContent = '<div class="row"><div class="col-md-12">'.$itemContent.'</div></div>';
            }

            $itemDate = $this->prepareDateTime($this->extractTagByName($entryXml, 'wp:post_date')[0]);
            $itemPublishedAt = null;
            if (ContentImportSettings::POST_STATUS_PUBLISH === $itemStatus
                && ContentImportSettings::POST_STATUS_SAVE_AS_ORIGINAL === $import->getSettings()['postSaveStatus']) {
                $itemPublishedAt = $itemDate;
            }

            $itemCategoriesNames = $this->extractTagByName($entryXml, 'category');

            if (self::TYPE_POST === $itemType) {
                $postsData[$itemId] = [
                    'title' => u($itemTitle)->slice(0, 200)->toString(),
                    'slug' => $this->slugger->slug($itemTitle)->lower()->slice(0, 200)->toString(),
                    'description' => $itemDescription,
                    'content' => $itemContent,
                    'created_at' => $itemDate->format('Y-m-h H:i:s'),
                    'updated_at' => $itemDate->format('Y-m-h H:i:s'),
                    'published_at' => $itemPublishedAt?->format('Y-m-h H:i:s'),

                    // Relationships
                    'image_url' => null,
                    'categories_names' => $itemCategoriesNames,
                ];
            } elseif (self::TYPE_PAGE === $itemType) {
                $pagesData[$itemId] = [
                    'title' => u($itemTitle)->slice(0, 200)->toString(),
                    'slug' => $this->slugger->slug($itemTitle)->lower()->slice(0, 200)->toString(),
                    'description' => $itemDescription,
                    'content' => $itemContent,
                    'created_at' => $itemDate->format('Y-m-h H:i:s'),
                    'updated_at' => $itemDate->format('Y-m-h H:i:s'),

                    // Relationships
                    'image_url' => null,
                    'categories_names' => $itemCategoriesNames,
                ];
            } elseif (self::TYPE_ATTACHMENT === $itemType) {
                $itemParentId = $this->extractTagByName($entryXml, 'wp:post_parent')[0];

                if ($itemParentId) {
                    $attachmentsData[$itemParentId] = $this->extractTagByName($entryXml, 'wp:attachment_url')[0];
                }
            }
        }

        // Link attachments to parent entities
        foreach ($attachmentsData as $parentId => $url) {
            if (isset($postsData[$parentId])) {
                $postsData[$parentId]['image_url'] = $url;
            } elseif (isset($pagesData[$parentId])) {
                $pagesData[$parentId]['image_url'] = $url;
            }
        }

        return [
            'posts' => $postsData,
            'pages' => $pagesData,
        ];
    }

    private function createCategoriesRegistry(ContentImport $import, array $rawData): array
    {
        $registry = [];
        foreach (['posts', 'pages'] as $type) {
            $registry[$type] = [];

            foreach ($rawData[$type] as $item) {
                foreach ($item['categories_names'] ?? [] as $name) {
                    if (trim($name ?: '')) {
                        $registry[$type][trim($name ?: '')] = null;
                    }
                }
            }
        }

        /*
         * Find existing categories IDs
         */
        $rootQb = $this->em->createQueryBuilder()
            ->select('c.id', 'c.name')
            ->where('c.project = :project')
            ->setParameter('project', $import->getProject());

        // Posts
        if ($registry['posts']) {
            $qb = clone $rootQb;
            $qb->from(PostCategory::class, 'c')->andWhere($qb->expr()->in('c.name', array_keys($registry['posts'])));

            foreach ($qb->getQuery()->getArrayResult() as $row) {
                $registry['posts'][$row['name']] = $row['id'];
            }
        }

        // Pages
        if ($registry['pages']) {
            $qb = clone $rootQb;
            $qb->from(PageCategory::class, 'c')->andWhere($qb->expr()->in('c.name', array_keys($registry['pages'])));

            foreach ($qb->getQuery()->getArrayResult() as $row) {
                $registry['pages'][$row['name']] = $row['id'];
            }
        }

        /*
         * Create missing categories
         */

        // Posts
        $insertValues = [];
        foreach ($registry['posts'] as $name => $id) {
            if (null === $id) {
                $registry['posts'][$name] = (string) Uid::random();

                $insertValues[] = sprintf(
                    '(nextval(\'website_posts_categories_id_seq\'), %s, %s, %s, %s, 100, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)',
                    $this->db->quote($registry['posts'][$name]),
                    $import->getProject()->getId(),
                    $this->db->quote(u($name)->slice(0, 40)->toString()),
                    $this->db->quote($this->slugger->slug($name)->lower()->slice(0, 40)->toString()),
                );
            }
        }

        if ($insertValues) {
            $this->db->executeStatement(
                'INSERT INTO website_posts_categories (id, uuid, project_id, name, slug, weight, created_at, updated_at) VALUES '.
                implode(', ', $insertValues)
            );
        }

        // Pages
        $insertValues = [];
        foreach ($registry['pages'] as $name => $id) {
            if (null === $id) {
                $registry['pages'][$name] = (string) Uid::random();

                $insertValues[] = sprintf(
                    '(nextval(\'website_pages_categories_id_seq\'), %s, %s, %s, %s, 100, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)',
                    $this->db->quote($registry['pages'][$name]),
                    $import->getProject()->getId(),
                    $this->db->quote(u($name)->slice(0, 40)->toString()),
                    $this->db->quote($this->slugger->slug($name)->lower()->slice(0, 40)->toString()),
                );
            }
        }

        if ($insertValues) {
            $this->db->executeStatement(
                'INSERT INTO website_pages_categories (id, uuid, project_id, name, slug, weight, created_at, updated_at) VALUES '.
                implode(', ', $insertValues)
            );
        }

        /*
         * Populate registry
         */
        $rootQb = $this->em->createQueryBuilder()
            ->select('c.id', 'c.name')
            ->where('c.project = :project')
            ->setParameter('project', $import->getProject());

        // Posts
        if ($registry['posts']) {
            $qb = clone $rootQb;
            $qb->from(PostCategory::class, 'c')->andWhere($qb->expr()->in('c.name', array_keys($registry['posts'])));

            foreach ($qb->getQuery()->getArrayResult() as $row) {
                $registry['posts'][$row['name']] = $row['id'];
            }
        }

        // Pages
        if ($registry['pages']) {
            $qb = clone $rootQb;
            $qb->from(PageCategory::class, 'c')->andWhere($qb->expr()->in('c.name', array_keys($registry['pages'])));

            foreach ($qb->getQuery()->getArrayResult() as $row) {
                $registry['pages'][$row['name']] = $row['id'];
            }
        }

        return $registry;
    }

    private function importPost(ContentImport $import, array $postsAuthorsRegistry, array $postsCategoriesRegistry, array $postData): void
    {
        $uuid = Uid::random()->toRfc4122();

        $insertData = [
            'id' => 'nextval(\'website_posts_id_seq\')',
            'uuid' => $this->db->quote($uuid),
            'project_id' => $import->getProject()?->getId(),
            'image_id' => 'null',
            'title' => $this->db->quote($postData['title']),
            'slug' => $this->db->quote($postData['slug']),
            'description' => $postData['description'] ? $this->db->quote($postData['description']) : 'null',
            'content' => $this->db->quote($postData['content']),
            'created_at' => $this->db->quote($postData['created_at']),
            'updated_at' => $this->db->quote($postData['updated_at']),
            'published_at' => $postData['published_at'] ? $this->db->quote($postData['published_at']) : 'null',
            'only_for_members' => 'false',
            'page_views' => '0',
        ];

        // Download image if there is one
        if ($postData['image_url']) {
            $insertData['image_id'] = $this->downloadImage($import->getProject(), $postData['image_url']) ?: 'null';
        }

        // Insert post
        $this->db->executeStatement(
            'INSERT INTO website_posts (id, uuid, project_id, image_id, title, slug, description, content, created_at, '.
            'updated_at, published_at, only_for_members, page_views) VALUES ('.implode(', ', $insertData).')'
        );

        $postId = $this->db->executeQuery('SELECT id FROM website_posts WHERE uuid = ?', [$uuid])->fetchAssociative()['id'];

        // Attach categories
        $insertValues = [];
        foreach ($postData['categories_names'] as $categoryName) {
            if (isset($postsCategoriesRegistry[$categoryName])) {
                $insertValues[] = sprintf('(%s, %s)', $postId, $postsCategoriesRegistry[$categoryName]);
            }
        }

        if ($insertValues) {
            $this->db->executeStatement(
                'INSERT INTO website_posts_posts_categories (post_id, post_category_id) '.
                'VALUES '.implode(', ', $insertValues).' ON CONFLICT DO NOTHING'
            );
        }

        // Attach authors
        $insertValues = [];
        foreach ($postsAuthorsRegistry as $authorId) {
            $insertValues[] = sprintf('(%s, %s)', $postId, $authorId);
        }

        if ($insertValues) {
            $this->db->executeStatement(
                'INSERT INTO website_posts_authors (post_id, trombinoscope_person_id) '.
                'VALUES '.implode(', ', $insertValues).' ON CONFLICT DO NOTHING'
            );
        }
    }

    private function importPage(ContentImport $import, array $pagesCategoriesRegistry, array $pageData): void
    {
        $uuid = Uid::random()->toRfc4122();

        $insertData = [
            'id' => 'nextval(\'website_pages_id_seq\')',
            'uuid' => $this->db->quote($uuid),
            'project_id' => $import->getProject()?->getId(),
            'image_id' => 'null',
            'title' => $this->db->quote($pageData['title']),
            'slug' => $this->db->quote($pageData['slug']),
            'description' => $pageData['description'] ? $this->db->quote($pageData['description']) : 'null',
            'content' => $this->db->quote($pageData['content']),
            'created_at' => $this->db->quote($pageData['created_at']),
            'updated_at' => $this->db->quote($pageData['updated_at']),
            'only_for_members' => 'false',
            'page_views' => '0',
            'parent_id' => 'null',
        ];

        // Download image if there is one
        if ($pageData['image_url']) {
            $insertData['image_id'] = $this->downloadImage($import->getProject(), $pageData['image_url']) ?: 'null';
        }

        // Insert post
        $this->db->executeStatement(
            'INSERT INTO website_pages (id, uuid, project_id, image_id, title, slug, description, content, created_at, '.
            'updated_at, only_for_members, page_views, parent_id) VALUES ('.implode(', ', $insertData).')'
        );

        $pageId = $this->db->executeQuery('SELECT id FROM website_pages WHERE uuid = ?', [$uuid])->fetchAssociative()['id'];

        // Attach categories
        $insertValues = [];
        foreach ($pageData['categories_names'] as $categoryName) {
            if (isset($pagesCategoriesRegistry[$categoryName])) {
                $insertValues[] = sprintf('(%s, %s)', $pageId, $pagesCategoriesRegistry[$categoryName]);
            }
        }

        if ($insertValues) {
            $this->db->executeStatement(
                'INSERT INTO website_pages_pages_categories (page_id, page_category_id) '.
                'VALUES '.implode(', ', $insertValues).' ON CONFLICT DO NOTHING'
            );
        }
    }

    private function extractTagByName(string $xml, string $tagName, $default = null): array
    {
        $pattern = '/<'.$tagName.'[^>]*>(.*?)<\/'.$tagName.'>/s';

        preg_match_all($pattern, $xml, $matches);

        if (empty($matches[1])) {
            return [$default];
        }

        return array_map(fn ($s) => $this->removeCDataFromString($s), $matches[1]);
    }

    private function prepareDateTime(?string $date): \DateTime
    {
        if ($date) {
            return new \DateTime($date);
        }

        return new \DateTime();
    }

    private function removeCDataFromString(string $string): string
    {
        return preg_replace_callback(
            '/<!\[CDATA\[(.*)\]\]>/s',
            static function (array $matches) {
                return $matches[1];
            },
            $string
        );
    }

    private function downloadImage(Project $project, string $imageUrl): ?string
    {
        if (!$filename = basename(parse_url($imageUrl, PHP_URL_PATH))) {
            return null;
        }

        if (!in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ContentImportSettings::ALLOWED_IMAGE_EXTENSIONS, true)) {
            return null;
        }

        $response = $this->httpClient->request('GET', $imageUrl);
        if (200 !== $response->getStatusCode()) {
            return null;
        }

        $localFile = @tempnam(md5(uniqid('', true)), 'content_import_image_');
        file_put_contents($localFile, $response->getContent());

        // upload local file to CDN and save to db
        $file = new UploadedFile($localFile, $filename);
        $upload = $this->cdnUploader->upload(CdnUploadRequest::createWebsiteContentMainImageRequest($project, $file));

        return $upload->getId();
    }
}

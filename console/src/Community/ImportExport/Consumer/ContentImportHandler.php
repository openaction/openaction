<?php

namespace App\Community\ImportExport\Consumer;

use App\Entity\Community\ContentImport;
use App\Entity\Community\Model\ContentImportSettings;
use App\Entity\Project;
use App\Entity\Website\Page;
use App\Entity\Website\Post;
use App\Repository\Community\ContentImportRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use XMLReader;

#[AsMessageHandler]
class ContentImportHandler
{
    public function __construct(
        private readonly ContentImportRepository $contentImportRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
        private readonly FilesystemReader $cdnStorage,
        private readonly RequestStack $requestStack
    ) {
    }

    public function __invoke(ContentImportMessage $message)
    {
        if (!$import = $this->contentImportRepository->find($message->getImportId())) {
            $this->logger->error('Import not found by its ID', ['id' => $message->getImportId()]);

            return true;
        }

        if ($import->getJob()->isFinished()) {
            $this->logger->error('Import finished', ['id' => $message->getImportId()]);

            return true;
        }

        if (ContentImportSettings::IMPORT_SOURCE_WORDPRESS === $import->getSource()) {
            $this->importWordPressContent($import);
        } else {
            throw new \RuntimeException('Import source handler not yet implemented!');
        }
    }

    private function importWordPressContent(ContentImport $import)
    {
        $this->logger->info('Parsing file', ['id' => $import->getId()]);
        $localFile = sys_get_temp_dir().'/citipo-wp-content-import-'.$import->getId().'.'.$import->getFile()->getExtension();
        file_put_contents($localFile, $this->cdnStorage->readStream($import->getFile()->getPathname()));

        $project = $this->getCurrentProject();
        $importSettings = $import->getSettings();


        try {
            $reader = XMLReader::open($localFile);
            $entries = [];

            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'item') {
                    // read and validate the entire <item> node
                    $entryXml = $reader->readOuterXml();

                    $itemId = $this->extractTagByName($entryXml, 'wp:post_id');
                    $itemType = $this->extractTagByName($entryXml, 'wp:post_type');
                    $itemStatus = $this->extractTagByName($entryXml, 'wp:status');
                    $itemTitle = $this->extractTagByName($entryXml, 'title');
                    $itemContent = $this->extractTagByName($entryXml, 'content:encoded', '');
                    $itemDescription = $this->extractTagByName($entryXml, 'description');
                    $itemPublishedAt = $this->prepareDateTime(
                        $this->extractTagByName($entryXml, 'wp:post_date')
                    );

                    if ($itemType) {
                        if (ContentImport::WORDPRESS_CONTENT_TYPE_PAGE === $itemType) {
                            // save page
                            $newPage = Page::createDefaultPage($project, $itemTitle, $itemContent);
                            $newPage->setDescription($itemDescription);
                            $newPage->setTitle($itemTitle);
                            $newPage->setContent($itemContent);
                            $newPage->setCreatedAt($itemPublishedAt);
                            $newPage->setUpdatedAt($itemPublishedAt);

                            $this->em->persist($newPage);
                            $this->em->flush();
                        } elseif (ContentImport::WORDPRESS_CONTENT_TYPE_POST === $itemType) {
                            // save post
                            $newPost = new Post($project, $itemTitle);
                            $newPost->setDescription($itemDescription);
                            $newPost->setTitle($itemTitle);
                            $newPost->setContent($itemContent);
                            $newPost->setCreatedAt($itemPublishedAt);
                            $newPost->setUpdatedAt($itemPublishedAt);

                            $publishedAt = null;
                            if (ContentImportSettings::POST_STATUS_SAVE_AS_ORIGINAL === $importSettings['postSaveStatus']) {
                                if (ContentImportSettings::POST_STATUS_PUBLISH === $itemStatus) {
                                    $publishedAt = $itemPublishedAt;
                                }
                            }
                            $newPost->setPublishedAt($publishedAt);

                            $this->em->persist($newPost);
                            $this->em->flush();
                        } elseif (ContentImport::WORDPRESS_CONTENT_TYPE_ATTACHMENT === $itemType) {
                            // save attachment

                        }
                    }
                }
            }

            $reader->close();
        } catch(\Exception $e) {
            echo "Exception: " . $e->getMessage();
            // TODO!! Handle exception
        }
    }

    private function extractTagByName(string $xml, string $tagName, $default = null): ?string
    {
        $pattern = '/<' . $tagName . '>(.*?)<\/' . $tagName . '>/';

        return preg_match($pattern, $xml, $matches) ?
            $this->removeCDataFromString($matches[1])
            :
            $default;
    }

    private function prepareDateTime(?string $date): \DateTime
    {
        if ($date) {
            return new \DateTime($date);
        }

        return new \DateTime();
    }

    private function getCurrentProject(): Project
    {
        $projectUuid = $this->requestStack->getMainRequest()->get('projectUuid');
        return $this->projectRepository->findOneByUuid($projectUuid);
    }

    private function removeCDataFromString(string $string): string
    {
        return preg_replace_callback(
            '/<!\[CDATA\[(.*)\]\]>/',
            static function (array $matches) {
                return $matches[1];
            },
            $string
        );
    }
}
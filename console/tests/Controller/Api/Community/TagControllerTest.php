<?php

namespace App\Tests\Controller\Api\Community;

use App\Entity\Community\Tag;
use App\Entity\Project;
use App\Repository\Community\TagRepository;
use App\Repository\ProjectRepository;
use App\Tests\ApiTestCase;

class TagControllerTest extends ApiTestCase
{
    public function testGetTags()
    {
        $client = self::createClient();

        $data = $this->apiRequest($client, 'GET', '/api/community/tags', self::CITIPO_TOKEN);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['apiToken' => self::CITIPO_TOKEN]);

        /** @var Tag[] $tags */
        $tags = static::getContainer()->get(TagRepository::class)->findAllByOrganization($project->getOrganization());

        $tagsArray = [];
        foreach ($tags as $key => $tag) {
            $tagsArray['data'][$key]['_resource'] = 'Tag';
            $tagsArray['data'][$key]['name'] = $tag->getName();
            $tagsArray['data'][$key]['slug'] = $tag->getSlug();
        }

        $this->assertApiResponse($data, $tagsArray);
    }

    public function testGetTagsNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/community/tags', null, 401);
    }

    public function testGetTagsInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/community/tags', 'invalid', 401);
    }
}

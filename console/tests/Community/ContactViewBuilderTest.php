<?php

namespace App\Tests\Community;

use App\Community\ContactViewBuilder;
use App\Entity\Community\Contact;
use App\Entity\Organization;
use App\Entity\Project;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactViewBuilderTest extends KernelTestCase
{
    public function provideConfigurators()
    {
        yield 'in-orga' => [
            static fn (ContactViewBuilder $builder, Organization $orga) => $builder->inOrganization($orga),
            [
                'olivie.gregoire@gmail.com',
                'tchalut@yahoo.fr',
                null,
                'brunella.courtemanche2@orange.fr',
                'a.compagnon@protonmail.com',
                'apolline.mousseau@rpr.fr',
            ],
        ];

        yield 'in-local-project' => [
            static fn (ContactViewBuilder $builder, Organization $orga, Project $localProject, Project $thematicProject) => $builder->inProject($localProject),
            [
                'tchalut@yahoo.fr',
                null,
                'brunella.courtemanche2@orange.fr',
                'a.compagnon@protonmail.com',
                'apolline.mousseau@rpr.fr',
            ],
        ];

        yield 'in-local-project-area' => [
            static fn (ContactViewBuilder $builder, Organization $orga, Project $localProject, Project $thematicProject) => $builder->inProject($localProject)->inAreas([39389989938296926]),
            [
                'tchalut@yahoo.fr',
                null,
                'brunella.courtemanche2@orange.fr',
                'apolline.mousseau@rpr.fr',
            ],
        ];

        yield 'in-thematic-project' => [
            static fn (ContactViewBuilder $builder, Organization $orga, Project $localProject, Project $thematicProject) => $builder->inProject($thematicProject),
            [
                'olivie.gregoire@gmail.com',
                null,
                'brunella.courtemanche2@orange.fr',
                'a.compagnon@protonmail.com',
            ],
        ];

        yield 'in-thematic-project-tags' => [
            static fn (ContactViewBuilder $builder, Organization $orga, Project $localProject, Project $thematicProject, array $tagsRegistry) => $builder->inProject($thematicProject)->withTags(
                [$tagsRegistry['StartWithTag']],
                ContactViewBuilder::FILTER_OR
            ),
            [
                'olivie.gregoire@gmail.com',
                'brunella.courtemanche2@orange.fr',
            ],
        ];

        yield 'in-area' => [
            static fn (ContactViewBuilder $builder, Organization $orga) => $builder->inOrganization($orga)->inAreas([39389989938296926]),
            [
                'tchalut@yahoo.fr',
                null,
                'brunella.courtemanche2@orange.fr',
                'apolline.mousseau@rpr.fr',
            ],
        ];

        yield 'with-tags-or' => [
            static fn (ContactViewBuilder $builder, Organization $orga, Project $localProject, Project $thematicProject, array $tagsRegistry) => $builder->inOrganization($orga)->withTags(
                [$tagsRegistry['ExampleTag'], $tagsRegistry['StartWithTag']],
                ContactViewBuilder::FILTER_OR
            ),
            [
                'olivie.gregoire@gmail.com',
                null,
                'brunella.courtemanche2@orange.fr',
                'a.compagnon@protonmail.com',
            ],
        ];

        yield 'with-tags-and' => [
            static fn (ContactViewBuilder $builder, Organization $orga, Project $localProject, Project $thematicProject, array $tagsRegistry) => $builder->inOrganization($orga)->withTags(
                [$tagsRegistry['ExampleTag'], $tagsRegistry['StartWithTag']],
                ContactViewBuilder::FILTER_AND
            ),
            [
                'olivie.gregoire@gmail.com',
            ],
        ];

        yield 'with-emails' => [
            static fn (ContactViewBuilder $builder, Organization $orga, Project $localProject, Project $thematicProject, array $tagsRegistry) => $builder->inOrganization($orga)->withEmails(['olivie.gregoire@gmail.com']),
            [
                'olivie.gregoire@gmail.com',
            ],
        ];

        yield 'with-phones' => [
            static fn (ContactViewBuilder $builder, Organization $orga, Project $localProject, Project $thematicProject, array $tagsRegistry) => $builder->inOrganization($orga)->withPhones(['+33757594629']),
            [
                'tchalut@yahoo.fr',
            ],
        ];

        yield 'only-members' => [
            static fn (ContactViewBuilder $builder, Organization $orga) => $builder->inOrganization($orga)->onlyMembers(),
            [
                'brunella.courtemanche2@orange.fr',
                'a.compagnon@protonmail.com',
                'apolline.mousseau@rpr.fr',
            ],
        ];

        yield 'only-newsletter-subscribers' => [
            static fn (ContactViewBuilder $builder, Organization $orga) => $builder->inOrganization($orga)->onlyNewsletterSubscribers(),
            [
                'olivie.gregoire@gmail.com',
                'brunella.courtemanche2@orange.fr',
                'a.compagnon@protonmail.com',
                'apolline.mousseau@rpr.fr',
            ],
        ];

        yield 'only-sms-subscribers' => [
            static fn (ContactViewBuilder $builder, Organization $orga) => $builder->inOrganization($orga)->onlySmsSubscribers(),
            [
                'olivie.gregoire@gmail.com',
                null,
                'brunella.courtemanche2@orange.fr',
                'a.compagnon@protonmail.com',
                'apolline.mousseau@rpr.fr',
            ],
        ];

        yield 'order-by-email' => [
            static fn (ContactViewBuilder $builder, Organization $orga) => $builder->inOrganization($orga)->orderBy('email', 'DESC'),
            [
                null,
                'tchalut@yahoo.fr',
                'olivie.gregoire@gmail.com',
                'brunella.courtemanche2@orange.fr',
                'apolline.mousseau@rpr.fr',
                'a.compagnon@protonmail.com',
            ],
        ];

        yield 'page' => [
            static fn (ContactViewBuilder $builder, Organization $orga) => $builder->inOrganization($orga)->setPage(2, 2),
            [
                null,
                'brunella.courtemanche2@orange.fr',
            ],
            6,
        ];
    }

    /**
     * @dataProvider provideConfigurators
     */
    public function testPaginate(callable $configurator, array $expectedResults)
    {
        self::bootKernel();

        $this->assertSame(
            $expectedResults,
            array_map(
                static fn (Contact $contact) => $contact->getEmail(),
                iterator_to_array($this->createConfiguredBuilder($configurator)->paginate())
            )
        );
    }

    /**
     * @dataProvider provideConfigurators
     */
    public function testCount(callable $configurator, array $expectedResults, int $expectedCount = null)
    {
        self::bootKernel();

        $this->assertSame(
            $expectedCount ?: count($expectedResults),
            $this->createConfiguredBuilder($configurator)->count()
        );
    }

    private function createConfiguredBuilder(callable $configurator): ContactViewBuilder
    {
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $localProject = self::getContainer()->get(ProjectRepository::class)->findOneByUuid('151f1340-9ad6-47c7-a8a5-838ff955eae7');
        $thematicProject = self::getContainer()->get(ProjectRepository::class)->findOneByUuid('062d7a3b-7cf3-48b0-b905-21f09844fb81');
        $tags = self::getContainer()->get(TagRepository::class)->findAllByOrganization($orga);

        $tagsRegistry = [];
        foreach ($tags as $tag) {
            $tagsRegistry[$tag->getName()] = $tag->getId();
        }

        $builder = self::getContainer()->get(ContactViewBuilder::class);

        return $configurator($builder, $orga, $localProject, $thematicProject, $tagsRegistry);
    }
}

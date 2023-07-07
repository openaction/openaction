<?php

namespace App\Controller\Console\Organization\Community;

use App\Controller\AbstractController;
use App\Entity\Community\Tag;
use App\Form\Community\MainTagsType;
use App\Form\Community\Model\MainTagsData;
use App\Form\Community\TagType;
use App\Platform\Permissions;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationMainTagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/community/configure')]
class ConfigurationController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/main-tags', name: 'console_organization_community_configure_main_tags')]
    public function mainTags(TagRepository $tagRepository, OrganizationMainTagRepository $repository, Request $request)
    {
        $orga = $this->getOrganization();

        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();

        $data = MainTagsData::createFromOrganization($orga);

        $form = $this->createForm(MainTagsType::class, $data, [
            'available_tags' => $tagRepository->findAllByOrganization($orga),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->updateMainTags($orga, $data);

            $this->addFlash('success', 'configuration.updated_success');

            return $this->redirectToRoute('console_organization_community_configure_main_tags', [
                'organizationUuid' => $orga->getUuid(),
            ]);
        }

        return $this->render('console/organization/community/configuration/mainTags.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tags', name: 'console_organization_community_configure_tags')]
    public function tagsIndex(TagRepository $repo)
    {
        $orga = $this->getOrganization();

        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();

        $tags = $repo->findBy(['organization' => $this->getOrganization()], ['name' => 'ASC']);

        return $this->render('console/organization/community/configuration/tags/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/tags/create', name: 'console_organization_community_configure_tags_create')]
    public function tagsCreate(Request $request)
    {
        return $this->createOrEdit(new Tag($this->getOrganization(), ''), $request, 'create.html.twig');
    }

    #[Route('/tags/{id}/edit', name: 'console_organization_community_configure_tags_edit')]
    public function tagsEdit(Tag $tag, Request $request)
    {
        $this->denyUnlessSameOrganization($tag);

        return $this->createOrEdit($tag, $request, 'edit.html.twig');
    }

    #[Route('/tags/{id}/delete', name: 'console_organization_community_configure_tags_delete')]
    public function tagsDelete(Tag $tag, Request $request)
    {
        $orga = $this->getOrganization();

        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($tag);

        if ($mainTag = $orga->getMainTag($tag)) {
            $this->em->remove($mainTag);
        }

        $this->em->remove($tag);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_organization_community_configure_tags', ['organizationUuid' => $orga->getUuid()]);
    }

    private function createOrEdit(Tag $tag, Request $request, string $template)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga = $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($tag);
            $this->em->flush();

            return $this->redirectToRoute('console_organization_community_configure_tags', ['organizationUuid' => $orga->getUuid()]);
        }

        return $this->render('console/organization/community/configuration/tags/'.$template, [
            'form' => $form->createView(),
            'tag' => $tag,
        ]);
    }
}

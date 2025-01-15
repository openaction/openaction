<?php

namespace App\Controller\Admin;

use App\Entity\OrganizationMember;
use App\Entity\Project;
use App\Proxy\DomainTokenCache;
use App\Repository\OrganizationMemberRepository;
use App\Repository\ProjectRepository;
use App\Search\TenantTokenManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractCrudController
{
    public function __construct(
        private readonly ProjectRepository $repository,
        private readonly DomainTokenCache $domainTokenCache,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public static function getSubscribedEvents()
    {
        return [
            AfterEntityPersistedEvent::class => ['refreshDomainsCache'],
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(100)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('name', 'Details')
                ->setTemplatePath('admin/projects/details.html.twig'),

            TextField::new('organization', 'Organization')
                ->setTemplatePath('admin/projects/organization.html.twig')
                ->hideOnForm(),

            TextField::new('websiteAccessUser', 'Status')
                ->setTemplatePath('admin/projects/status.html.twig')
                ->setSortable(false)
                ->hideOnForm(),

            DateTimeField::new('createdAt', 'Creation date')
                ->hideOnForm(),

            TextField::new('subDomain', 'Sub domain')
                ->onlyOnForms(),

            AssociationField::new('rootDomain', 'Root domain')
                ->onlyOnForms(),

            AssociationField::new('emailingDomain', 'Emailing domain')
                ->onlyOnForms(),

            BooleanField::new('websiteDisableGdprFields', 'Disable GDPR fields on website forms?')
                ->onlyOnForms(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
        ;
    }

    #[Route('/admin/projects/{id}/add-myself', name: 'admin_project_add_myself')]
    public function addMyself(OrganizationMemberRepository $repository, TenantTokenManager $ttm, Project $project)
    {
        if (!$repository->findMember($this->getUser(), $project->getOrganization())) {
            $ttm->refreshMemberCrmTenantToken(
                new OrganizationMember($project->getOrganization(), $this->getUser(), true),
                persist: true,
            );
        }

        return $this->redirectToRoute('console_project_home_start', [
            'projectUuid' => $project->getUuid(),
        ]);
    }

    #[Route('/admin/projects/export', name: 'admin_projects_export')]
    public function export()
    {
        $filename = sys_get_temp_dir().'/'.date('Y-m-d').'-projects.xlsx';
        touch($filename);

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filename);

        $headerAdded = false;
        foreach ($this->repository->getExportData() as $project) {
            if (!$headerAdded) {
                $row = WriterEntityFactory::createRowFromArray(array_keys($project));
                $row->setStyle((new StyleBuilder())->setFontBold()->setCellAlignment(CellAlignment::CENTER)->build());

                $writer->addRow($row);
                $headerAdded = true;
            }

            $writer->addRow(WriterEntityFactory::createRowFromArray($project));
        }

        $writer->close();

        $response = new Response(file_get_contents($filename));
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            date('Y-m-d').'-projects.xlsx',
        ));

        return $response;
    }

    public function manageTurnstile(Request $request): Response
    {
        $activeProjects = $this->repository->findAllForActiveOrganizations();

        $data = [];
        $builder = $this->createFormBuilder();

        foreach ($activeProjects as $project) {
            $data[$project->getId().'_sitekey'] = $project->getWebsiteTurnstileSiteKey();
            $data[$project->getId().'_secretkey'] = $project->getWebsiteTurnstileSecretKey();

            $builder->add($project->getId().'_sitekey', TextType::class, ['required' => false]);
            $builder->add($project->getId().'_secretkey', TextType::class, ['required' => false]);
        }

        $builder->setData($data);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $batch = [];
            foreach ($activeProjects as $project) {
                $id = $project->getId();

                $batch[$id] = [
                    'siteKey' => $data[$id.'_sitekey'],
                    'secretKey' => $data[$id.'_secretkey'],
                ];
            }

            $this->repository->updateTurnstileConfigs($batch);

            $this->addFlash('success', 'Saved');

            return $this->redirect($this->adminUrlGenerator
                ->setController(ProjectController::class)
                ->setAction('manageTurnstile')
                ->setEntityId(null)
            );
        }

        return $this->render('admin/projects/manage_turnstile.html.twig', [
            'form' => $builder->getForm(),
            'projects' => $activeProjects,
        ]);
    }

    public function refreshDomainsCache(): void
    {
        $this->domainTokenCache->refresh();
    }
}

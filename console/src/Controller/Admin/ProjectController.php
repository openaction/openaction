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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractCrudController
{
    private ProjectRepository $repository;
    private DomainTokenCache $domainTokenCache;

    public function __construct(ProjectRepository $repository, DomainTokenCache $domainTokenCache)
    {
        $this->repository = $repository;
        $this->domainTokenCache = $domainTokenCache;
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

    public function refreshDomainsCache()
    {
        $this->domainTokenCache->refresh();
    }
}

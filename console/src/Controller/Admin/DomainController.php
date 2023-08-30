<?php

namespace App\Controller\Admin;

use App\Entity\Domain;
use App\Form\Admin\AddDomainType;
use App\Form\Admin\Model\AddDomainData;
use App\Proxy\DomainManager;
use App\Repository\DomainRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DomainController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Domain::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['name' => 'ASC'])
            ->setPaginatorPageSize(100)
            ->overrideTemplates([
                'crud/index' => 'admin/domains/list.html.twig',
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('name', 'Name')
                ->setTemplatePath('admin/domains/name.html.twig')
                ->hideOnForm(),

            AssociationField::new('organization', 'Organization'),

            ArrayField::new('configurationStatus', 'Status')
                ->setTemplatePath('admin/domains/status.html.twig')
                ->hideOnForm(),

            BooleanField::new('managedAutomatically', 'Managed?')
                ->renderAsSwitch(false),

            DateTimeField::new('lastCheckedAt', 'Last checked at')
                ->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ;
    }

    #[Route('/admin/domains/add', name: 'admin_domains_add')]
    public function add(DomainRepository $repository, DomainManager $domainManager, AdminUrlGenerator $urlGenerator, Request $request)
    {
        $data = new AddDomainData();

        $form = $this->createForm(AddDomainType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($repository->findOneBy(['name' => strtolower(trim($data->name))])) {
                $form->addError(new FormError('This domain already exists.'));
            } else {
                $domainManager->createDomain($data->organization, strtolower(trim($data->name)));

                return $this->redirect($urlGenerator->setController(__CLASS__)->generateUrl());
            }
        }

        return $this->render('admin/add_domain.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

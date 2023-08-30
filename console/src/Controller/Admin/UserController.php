<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractCrudController
{
    private bool $isOnPremise;

    public function __construct(bool $isOnPremise)
    {
        $this->isOnPremise = $isOnPremise;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
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
        yield IdField::new('id', 'ID')
            ->hideOnForm();

        yield TextField::new('fullName', 'Details')
            ->hideOnForm()
            ->setTemplatePath('admin/users/details.html.twig');

        yield TextField::new('email', 'Email address')
            ->onlyOnDetail();

        yield TextField::new('firstName', 'First name')
            ->hideOnIndex();

        yield TextField::new('lastName', 'Last name')
            ->hideOnIndex();

        yield BooleanField::new('isAdmin', 'Is admin?')
            ->hideOnIndex()
            ->renderAsSwitch(false);

        if (!$this->isOnPremise) {
            yield BooleanField::new('isPartner', 'Is partner?')
                ->renderAsSwitch(false);

            yield TextField::new('partnerName', 'Name as a partner')
                ->hideOnIndex();
        }

        yield TextField::new('secretResetPassword', 'Reset password URL')
            ->setTemplatePath('admin/users/secretResetPassword.html.twig')
            ->onlyOnDetail();

        yield AssociationField::new('visits', 'Console visits')
            ->hideOnForm()
            ->setTemplatePath('admin/users/visits.html.twig');

        yield DateTimeField::new('createdAt', 'Creation date')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(
                Crud::PAGE_INDEX,
                Action::new('impersonate', 'Impersonate')
                    ->linkToRoute('admin_user_impersonate', fn (User $user) => ['id' => $user->getId()])
            )
            ->add(
                Crud::PAGE_DETAIL,
                Action::new('password', 'Reset password')
                    ->linkToRoute('admin_user_password', fn (User $user) => ['id' => $user->getId()])
            )
        ;
    }

    #[Route('/admin/users/{id}/impersonate', name: 'admin_user_impersonate')]
    public function impersonate(UserRepository $repository, int $id)
    {
        if (!$user = $repository->find($id)) {
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute('homepage_redirect', ['_switch_user' => $user->getEmail()]);
    }

    #[Route('/admin/users/{id}/password', name: 'admin_user_password')]
    public function password(UserRepository $repository, EntityManagerInterface $em, AdminUrlGenerator $urlGenerator, int $id)
    {
        if (!$user = $repository->find($id)) {
            throw $this->createNotFoundException();
        }

        $user->createForgotPasswordSecret();

        $em->persist($user);
        $em->flush();

        return $this->redirect(
            $urlGenerator->setController(__CLASS__)
                ->setAction('detail')
                ->setEntityId($user->getId())
                ->generateUrl()
        );
    }
}

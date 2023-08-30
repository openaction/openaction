<?php

namespace App\Controller\Admin;

use App\Entity\Announcement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnnouncementController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Announcement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['date' => 'DESC'])
            ->setPaginatorPageSize(100)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('locale', 'Locale'),

            TextField::new('title', 'Title'),

            TextField::new('description', 'Description')
                ->setFormType(TextareaType::class)
                ->onlyOnForms(),

            TextField::new('linkText', 'Link text')
                ->onlyOnForms(),

            UrlField::new('linkUrl', 'Link URL')
                ->onlyOnForms(),

            DateTimeField::new('date', 'Date')
                ->renderAsChoice(),
        ];
    }
}

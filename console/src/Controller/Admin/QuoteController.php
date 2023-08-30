<?php

namespace App\Controller\Admin;

use App\Entity\Billing\Quote;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use League\Flysystem\FilesystemReader;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quote::class;
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
            IdField::new('id', 'ID'),
            TextField::new('company', 'Company'),
            AssociationField::new('organization', 'Billing details')->setTemplatePath('admin/orders/organization.html.twig'),
            TextField::new('amountDescription', 'Amount'),
            DateTimeField::new('createdAt', 'Created at'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(
                Crud::PAGE_INDEX,
                Action::new('download', 'Download PDF')
                    ->linkToRoute('admin_quote_download', fn (Quote $q) => ['id' => $q->getId()])
                    ->displayIf(fn (Quote $q) => null !== $q->getPdf())
            )
        ;
    }

    #[Route('/quote/{id}/download', name: 'admin_quote_download')]
    public function download(FilesystemReader $cdnStorage, Quote $quote)
    {
        $path = $quote->getPdf()->getPathname();

        $response = new StreamedResponse(
            static function () use ($cdnStorage, $path) {
                stream_copy_to_stream($cdnStorage->readStream($path), fopen('php://output', 'wb'));
            }
        );

        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, 'quote.pdf');

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}

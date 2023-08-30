<?php

namespace App\Controller\Admin;

use App\Billing\BillingManager;
use App\Billing\Model\OrderLine;
use App\Entity\Billing\Model\OrderAction;
use App\Entity\Billing\Model\OrderRecipient;
use App\Entity\Billing\Order;
use App\Entity\Organization;
use App\Platform\Companies;
use App\Repository\OrganizationRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
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
            DateTimeField::new('paidAt', 'Status')->setTemplatePath('admin/orders/status.html.twig'),
            TextField::new('mollieId', 'Mollie ID')->setTemplatePath('admin/orders/mollieId.html.twig'),
            TextField::new('action', 'Action')->setTemplatePath('admin/orders/action.html.twig'),
            DateTimeField::new('createdAt', 'Created at'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::new('create', 'New order')->linkToRoute('admin_order_new')->createAsGlobalAction())
            ->add(Crud::PAGE_INDEX, Action::new('download', 'Download')
                ->linkToRoute('admin_order_download', static fn (Order $order) => ['id' => $order->getId()])
                ->setHtmlAttributes(['target' => '_blank'])
                ->displayIf(static fn (Order $order) => null !== $order->getInvoiceNumber())
            )
        ;
    }

    #[Route('/admin/orders/new', name: 'admin_order_new')]
    public function newOrder(BillingManager $billingManager, AdminUrlGenerator $urlGenerator, Request $request)
    {
        $form = $this->createFormBuilder([
            'recipientLocale' => 'fr_FR',
            'line1_vat_rate' => 20,
            'line1_quantity' => 1,
            'line2_vat_rate' => 20,
            'line2_quantity' => 1,
            'reduction' => 0,
        ])
            ->add('organization', EntityType::class, [
                'class' => Organization::class,
                'query_builder' => static fn (OrganizationRepository $r) => $r->createQueryBuilder('o')->orderBy('o.name', 'ASC'),
                'help' => 'Please check the organization billing details before issuing the order as it will not be editable afterwards.',
            ])
            ->add('recipientFirstName', TextType::class, ['required' => true])
            ->add('recipientLastName', TextType::class, ['required' => true])
            ->add('recipientEmail', EmailType::class, ['required' => true])
            ->add('recipientLocale', TextType::class, ['required' => true])
            ->add('line1_title', TextType::class)
            ->add('line1_text', TextareaType::class)
            ->add('line1_vat_rate', TextType::class)
            ->add('line1_amount', TextType::class)
            ->add('line1_quantity', TextType::class)
            ->add('line2_title', TextType::class, ['required' => false])
            ->add('line2_text', TextareaType::class, ['required' => false])
            ->add('line2_vat_rate', TextType::class, ['required' => false])
            ->add('line2_amount', TextType::class, ['required' => false])
            ->add('line2_quantity', TextType::class, ['required' => false])
            ->add('reduction', TextType::class, ['required' => false])
            ->add('enableMolliePaymentLink', CheckboxType::class, ['required' => false])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $totalAmount = 0;

            $lines = [];
            $lines[] = new OrderLine(
                OrderLine::TYPE_PRODUCT,
                $data['line1_title'],
                $data['line1_text'],
                (int) $data['line1_quantity'],
                (float) $data['line1_amount'],
                (float) $data['line1_vat_rate'],
            );

            $totalAmount += $data['line1_amount'] * $data['line1_quantity'] * (1 + $data['line1_vat_rate'] / 100);

            if ($data['line2_text']) {
                $lines[] = new OrderLine(
                    OrderLine::TYPE_PRODUCT,
                    $data['line2_title'],
                    $data['line2_text'],
                    (int) $data['line2_quantity'],
                    (float) $data['line2_amount'],
                    (float) $data['line2_vat_rate'],
                );

                $totalAmount += $data['line2_amount'] * $data['line2_quantity'] * (1 + $data['line2_vat_rate'] / 100);
            }

            if ($data['reduction']) {
                $lines[] = new OrderLine(
                    OrderLine::TYPE_DISCOUNT,
                    'Reduction',
                    '-'.$data['reduction'].' %',
                    1,
                    -($totalAmount * ($data['reduction'] / 100)),
                    0,
                );
            }

            // Create order
            if ($data['enableMolliePaymentLink']) {
                $order = $billingManager->createMollieOrder(
                    Companies::CITIPO,
                    $data['organization'],
                    new OrderRecipient($data['recipientFirstName'], $data['recipientLastName'], $data['recipientEmail'], $data['recipientLocale']),
                    new OrderAction(OrderAction::NOTHING),
                    $lines,
                );
            } else {
                $order = $billingManager->createManualOrder(
                    Companies::CITIPO,
                    $data['organization'],
                    new OrderRecipient($data['recipientFirstName'], $data['recipientLastName'], $data['recipientEmail'], $data['recipientLocale']),
                    new OrderAction(OrderAction::NOTHING),
                    $lines,
                );
            }

            $billingManager->markOrderToPay($order);

            return $this->redirect($urlGenerator->setController(__CLASS__)->generateUrl());
        }

        return $this->render('admin/orders/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/orders/{id}/download', name: 'admin_order_download')]
    public function download(Order $order)
    {
        return $this->render('billing/invoice.html.twig', [
            'invoice' => $order,
            'company' => Companies::BILLING[$order->getCompany()],
        ]);
    }
}

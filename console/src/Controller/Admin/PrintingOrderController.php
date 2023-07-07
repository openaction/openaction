<?php

namespace App\Controller\Admin;

use App\Cdn\CdnRouter;
use App\Community\Printing\PrintingWorkflow;
use App\Entity\Community\PrintingCampaign;
use App\Entity\Community\PrintingOrder;
use App\Platform\Companies;
use App\Platform\Features;
use App\Repository\Billing\QuoteRepository;
use App\Repository\Community\PrintingCampaignRepository;
use App\Repository\Community\PrintingOrderRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrintingOrderController extends AbstractCrudController
{
    private ProjectRepository $projectRepository;
    private QuoteRepository $quoteRepository;
    private PrintingOrderRepository $orderRepository;
    private PrintingCampaignRepository $campaignRepository;
    private PrintingWorkflow $workflow;
    private CdnRouter $cdnRouter;

    public function __construct(ProjectRepository $pr, QuoteRepository $qr, PrintingOrderRepository $or, PrintingCampaignRepository $cr, PrintingWorkflow $w, CdnRouter $r)
    {
        $this->projectRepository = $pr;
        $this->quoteRepository = $qr;
        $this->orderRepository = $or;
        $this->campaignRepository = $cr;
        $this->workflow = $w;
        $this->cdnRouter = $r;
    }

    public static function getEntityFqcn(): string
    {
        return PrintingOrder::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($statusFilter = $searchDto->getRequest()->get('status')) {
            if ('ordered' === $statusFilter) {
                $qb->andWhere('entity.order IS NOT NULL');
            } elseif ('draft' === $statusFilter) {
                $qb->andWhere('entity.order IS NULL');
            }
        }

        return $qb;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['order.createdAt' => 'DESC', 'createdAt' => 'DESC'])
            ->setPaginatorPageSize(100)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('reference', 'Reference')
                ->setTemplatePath('admin/print/reference.html.twig')
                ->hideOnForm(),

            AssociationField::new('campaigns', 'Products')
                ->setTemplatePath('admin/print/products.html.twig')
                ->hideOnForm(),

            BooleanField::new('status', 'Status')
                ->setTemplatePath('admin/print/status.html.twig')
                ->hideOnForm(),

            DateTimeField::new('order.createdAt', 'Ordered at')
                ->hideOnForm(),
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
                Action::new('markPaid', 'Mark this order as paid')
                    ->linkToRoute('admin_print_mark_paid', fn (PrintingOrder $o) => ['id' => $o->getId()])
                    ->displayIf(fn (PrintingOrder $o) => null !== $o->getOrder())
            )
        ;
    }

    #[Route('/print/{id}/mark-paid', name: 'admin_print_mark_paid')]
    public function markPaid(AdminUrlGenerator $urlGenerator, PrintingOrder $order, Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->workflow->receivePayment($order);

            return $this->redirect($urlGenerator->setController(self::class)->set('status', 'ordered')->generateUrl());
        }

        return $this->render('admin/print/mark_paid.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/print/dashboard', name: 'admin_print_dashboard')]
    public function dashboard()
    {
        return $this->render('admin/print/dashboard.html.twig', [
            'print_projects_count' => $this->projectRepository->countByTool(Features::TOOL_COMMUNITY_PRINTING),
            'quotes_count' => $this->quoteRepository->count(['company' => Companies::CID]),
            'draft_count' => $this->orderRepository->countByOrderedStatus(true),
            'ordered_count' => $this->orderRepository->countByOrderedStatus(true),
            'waiting_payment_count' => $this->orderRepository->countWaitingForPayment(),
        ]);
    }

    #[Route('/print/export-to-print', name: 'admin_print_export_to_print')]
    public function exportToPrint()
    {
        $response = new Response(file_get_contents($this->createExportFile($this->campaignRepository->createAdminToPrintExport())));
        $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            date('Y-m-d H:i:s').'-to-print.xlsx'
        ));

        return $response;
    }

    #[Route('/print/export-ordered', name: 'admin_print_export_ordered')]
    public function exportOrdered()
    {
        $response = new Response(file_get_contents($this->createExportFile($this->campaignRepository->createAdminOrderedExport())));
        $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            date('Y-m-d H:i:s').'-ordered.xlsx'
        ));

        return $response;
    }

    #[Route('/print/export-all', name: 'admin_print_export_all')]
    public function exportAll()
    {
        $response = new Response(file_get_contents($this->createExportFile($this->campaignRepository->createAdminAllExport())));
        $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            date('Y-m-d H:i:s').'-all.xlsx'
        ));

        return $response;
    }

    private function createExportFile(iterable $campaigns): string
    {
        $filename = sys_get_temp_dir().'/printing-orders.xlsx';
        file_put_contents($filename, '');

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filename);

        $headerAdded = false;
        foreach ($campaigns as $campaign) {
            $data = $this->exportCampaign($campaign);

            if (!$headerAdded) {
                $writer->addRow(WriterEntityFactory::createRowFromArray(array_keys($data)));
                $headerAdded = true;
            }

            $writer->addRow(WriterEntityFactory::createRowFromArray($data));
        }

        $writer->close();

        return $filename;
    }

    private function exportCampaign(PrintingCampaign $campaign): array
    {
        $order = $campaign->getPrintingOrder();

        return [
            'uuid' => $campaign->getUuid()->toRfc4122(),
            'commandUuid' => $order->getUuid()->toRfc4122(),
            'commandTotalProducts' => $order->getCampaigns()->count(),
            'product' => $campaign->getProduct(),
            'source' => $campaign->getProduct() ? $this->cdnRouter->generateUrl($campaign->getProduct()) : '',
            'batValidatedAt' => $campaign->getBatValidatedAt()?->format('Y-m-d H:i:s') ?: '',
            'paidAt' => $order->getOrder()?->getPaidAt()?->format('Y-m-d H:i:s') ?: '',
            'printedAt' => $campaign->getPrintedAt()?->format('Y-m-d H:i:s') ?: '',
            'deliveredAt' => $campaign->getDeliveredAt()?->format('Y-m-d H:i:s') ?: '',
            'withEnveloping' => $order->isWithEnveloping(),
            'deliveryAddressed' => $order->isDeliveryAddressed(),
            'deliveryMainAddressName' => $order->getDeliveryMainAddressName(),
            'deliveryMainAddressStreet1' => $order->getDeliveryMainAddressStreet1(),
            'deliveryMainAddressStreet2' => $order->getDeliveryMainAddressStreet2(),
            'deliveryMainAddressZipCode' => $order->getDeliveryMainAddressZipCode(),
            'deliveryMainAddressCity' => $order->getDeliveryMainAddressCity(),
            'deliveryMainAddressCountry' => $order->getDeliveryMainAddressCountry(),
            'deliveryMainAddressIntructions' => $order->getDeliveryMainAddressInstructions(),
            'deliveryMainAddressProvider' => $order->getDeliveryMainAddressProvider(),
            'deliveryMainAddressTrackingCode' => $order->getDeliveryMainAddressTrackingCode(),
            'deliveryPosterAddressName' => $order->getDeliveryPosterAddressName(),
            'deliveryPosterAddressStreet1' => $order->getDeliveryPosterAddressStreet1(),
            'deliveryPosterAddressStreet2' => $order->getDeliveryPosterAddressStreet2(),
            'deliveryPosterAddressZipCode' => $order->getDeliveryPosterAddressZipCode(),
            'deliveryPosterAddressCity' => $order->getDeliveryPosterAddressCity(),
            'deliveryPosterAddressCountry' => $order->getDeliveryPosterAddressCountry(),
            'deliveryPosterAddressIntructions' => $order->getDeliveryPosterAddressInstructions(),
            'deliveryPosterAddressProvider' => $order->getDeliveryPosterAddressProvider(),
            'deliveryPosterAddressTrackingCode' => $order->getDeliveryPosterAddressTrackingCode(),
            'deliveryQuantity' => $campaign->getQuantity(),
            'recipientCandidate' => $order->getRecipientCandidate(),
            'recipientDepartment' => $order->getRecipientDepartment(),
            'recipientCirconscription' => $order->getRecipientCirconscription(),
            'recipientFirstName' => $order->getRecipientFirstName(),
            'recipientLastName' => $order->getRecipientLastName(),
            'recipientEmail' => $order->getRecipientEmail(),
            'recipientPhone' => $order->getRecipientPhone(),
            'billingName' => $order->getProject()->getOrganization()->getBillingName(),
            'billingAddressStreetLine1' => $order->getProject()->getOrganization()->getBillingAddressStreetLine1(),
            'billingAddressStreetLine2' => $order->getProject()->getOrganization()->getBillingAddressStreetLine2(),
            'billingAddressZipCode' => $order->getProject()->getOrganization()->getBillingAddressPostalCode(),
            'billingAddressCity' => $order->getProject()->getOrganization()->getBillingAddressCity(),
            'billingAddressCountry' => $order->getProject()->getOrganization()->getBillingAddressCountry(),
        ];
    }
}

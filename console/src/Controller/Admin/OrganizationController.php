<?php

namespace App\Controller\Admin;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Platform\Plans;
use App\Repository\OrganizationMemberRepository;
use App\Search\TenantTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrganizationController extends AbstractCrudController
{
    public function __construct(
        private readonly OrganizationMemberRepository $memberRepository,
        private readonly bool $isOnPremise,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Organization::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(50)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        if ($this->isOnPremise) {
            return $filters;
        }

        return $filters
            ->add(ChoiceFilter::new('subscriptionPlan')->setChoices([
                'Essential' => Plans::ESSENTIAL,
                'Standard' => Plans::STANDARD,
                'Premium' => Plans::PREMIUM,
                'Organization' => Plans::ORGANIZATION,
            ]))
            ->add('subscriptionTrialing')
            ->add('subscriptionCurrentPeriodEnd')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm();

        yield FormField::addPanel('Details')
            ->setIcon('fa fa-id-card')
            ->setHelp('Organization details configures how the organization is displayed in the platform.');

        yield TextField::new('name', 'Name')
            ->setTemplatePath('admin/organizations/name.html.twig')
            ->setColumns(6);

        if (!$this->isOnPremise) {
            yield AssociationField::new('partner', 'Partner')
                ->hideOnIndex()
                ->setQueryBuilder(static function (QueryBuilder $qb) {
                    $qb->where('entity.isPartner = TRUE');
                })
                ->setRequired(false)
                ->setColumns(6);

            yield BooleanField::new('showPreview', 'Show preview features?')
                ->onlyOnForms();

            yield FormField::addPanel('Email provider')
                ->setIcon('fa fa-envelope')
                ->setHelp('Provider to send emails for this organization. By default: Sendgrid.');

            yield ChoiceField::new('emailProvider', 'Email provider')
                ->setChoices([
                    'Sendgrid' => 'sendgrid',
                    'Postmark' => 'postmark',
                    'Mailchimp' => 'mailchimp',
                ]);

            yield TextField::new('mailchimpServerPrefix', 'Mailchimp server prefix')
                ->setColumns(4)
                ->onlyOnForms();

            yield TextField::new('mailchimpApiKey', 'Mailchimp API key')
                ->setColumns(4)
                ->onlyOnForms();

            yield TextField::new('mailchimpAudienceName', 'Mailchimp audience name')
                ->setColumns(4)
                ->onlyOnForms();

            yield FormField::addPanel('Integrations')
                ->setIcon('fa fa-cogs')
                ->setHelp('Configured native integrations for this organization.');

            yield TextField::new('quorumToken', 'Quorum API token')
                ->onlyOnForms()
                ->setColumns(6);

            yield TextField::new('quorumDefaultCity', 'Quorum default city name')
                ->onlyOnForms()
                ->setColumns(6);

            yield TextField::new('spallianEndpoint', 'Spallian API endpoint')
                ->onlyOnForms();
        }

        yield FormField::addPanel('Subscription')
            ->setIcon('fa fa-sync')
            ->setHelp('Subscription details configures accessible features for the organization.');

        if (!$this->isOnPremise) {
            yield ChoiceField::new('subscriptionPlan', 'Plan')
                ->setTemplatePath('admin/organizations/plan.html.twig')
                ->setChoices([
                    'Essential' => Plans::ESSENTIAL,
                    'Standard' => Plans::STANDARD,
                    'Premium' => Plans::PREMIUM,
                    'Organization' => Plans::ORGANIZATION,
                ])
                ->setColumns(6);

            yield DateTimeField::new('subscriptionCurrentPeriodEnd', 'End date of the current period')
                ->renderAsChoice()
                ->onlyOnForms()
                ->setColumns(6);

            yield BooleanField::new('subscriptionTrialing', 'Is trial?')
                ->onlyOnForms();

            yield DateTimeField::new('subscriptionCurrentPeriodEnd', 'Current period end')
                ->setTemplatePath('admin/organizations/is_expired.html.twig')
                ->hideOnForm();
        }

        yield NumberField::new('projectsSlots', 'Stats')
            ->setTemplatePath('admin/organizations/stats.html.twig')
            ->setSortable(false)
            ->hideOnForm();

        yield NumberField::new('projectsSlots', 'Projects slots')
            ->onlyOnForms()
            ->setColumns(4);

        yield NumberField::new('creditsBalance', 'Emails credits balance')
            ->onlyOnForms()
            ->setColumns(4);

        yield NumberField::new('textsCreditsBalance', 'Texts credits balance')
            ->onlyOnForms()
            ->setColumns(4);

        yield FormField::addPanel('Specializations')
            ->setIcon('fa fa-terminal')
            ->setHelp('Customize the behavior of the Console for this organization.');

        if (!$this->isOnPremise) {
            yield TextareaField::new('consoleCustomCss', 'Custom Console CSS')
                ->setNumOfRows(15)
                ->onlyOnForms();

            yield TextField::new('textingSenderCode', 'Sender code for text messages (uppercase, max 12 characters)')
                ->onlyOnForms();
        }

        yield FormField::addPanel('Billing')
            ->setIcon('fa fa-file-invoice-dollar')
            ->setHelp('Billing details are used for revenue statistics and recurring payments if enabled.');

        if (!$this->isOnPremise) {
            yield NumberField::new('billingPricePerMonth', 'Price paid per month (in cents)')
                ->onlyOnForms();
        }

        yield TextField::new('billingName', 'Billing name')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('billingEmail', 'Billing email')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('billingAddressStreetLine1', 'Address line 1')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('billingAddressStreetLine2', 'Address line 2')
            ->onlyOnForms()
            ->setColumns(6);

        yield TextField::new('billingAddressPostalCode', 'Postal code')
            ->onlyOnForms()
            ->setColumns(3);

        yield TextField::new('billingAddressCity', 'City')
            ->onlyOnForms()
            ->setColumns(5);

        yield CountryField::new('billingAddressCountry', 'Country')
            ->onlyOnForms()
            ->setColumns(4);

        yield TextField::new('billingTaxId', 'Tax ID')
            ->onlyOnForms()
            ->setColumns(4);

        yield DateTimeField::new('createdAt', 'Created')
            ->setTemplatePath('admin/organizations/created_at.html.twig')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(
                Crud::PAGE_INDEX,
                Action::new('upate_white_label', 'Update white label details')
                    ->linkToRoute('admin_organization_upate_white_label', fn (Organization $o) => ['id' => $o->getId()])
            )
            ->add(
                Crud::PAGE_INDEX,
                Action::new('impersonate', 'Impersonate')
                    ->linkToRoute('admin_organization_impersonate', fn (Organization $o) => ['id' => $o->getId()])
            )
        ;
    }

    #[Route('/admin/organizations/{id}/impersonate', name: 'admin_organization_impersonate')]
    public function impersonate(Organization $organization, Request $request)
    {
        if (!$user = $this->memberRepository->findOneAdmin($organization)?->getMember()) {
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute(
            $request->query->get('route', 'homepage_redirect'),
            array_merge($request->query->all(), ['_switch_user' => $user->getEmail()]),
        );
    }

    #[Route('/admin/organizations/{id}/add-myself', name: 'admin_organization_add_myself')]
    public function addMyself(OrganizationMemberRepository $repository, TenantTokenManager $ttm, Organization $organization)
    {
        if (!$repository->findMember($this->getUser(), $organization)) {
            $ttm->refreshMemberCrmTenantToken(
                new OrganizationMember($organization, $this->getUser(), true),
                persist: true,
            );
        }

        return $this->redirectToRoute('console_organization_projects', [
            'organizationUuid' => $organization->getUuid(),
        ]);
    }

    #[Route('/admin/organizations/{id}/update-white-label', name: 'admin_organization_upate_white_label', methods: ['GET', 'POST'])]
    public function updateWhiteLabel(
        CdnUploader $cdnUploader,
        EntityManagerInterface $manager,
        AdminUrlGenerator $adminUrlGenerator,
        Organization $organization,
        Request $request,
    ) {
        $form = $this->createFormBuilder(['name' => $organization->getWhiteLabelName()])
            ->add('name', TextType::class, [
                'label' => 'Custom name',
                'required' => false,
                'constraints' => [new NotBlank(), new Length(max: 25)],
            ])
            ->add('logo', FileType::class, [
                'label' => 'Custom logo',
                'required' => false,
                'constraints' => [new NotBlank(), new Image(maxSize: '5M')],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $upload = null;
            if ($uploadedLogo = $form->get('logo')->getData()) {
                $upload = $cdnUploader->upload(CdnUploadRequest::createOrganizationWhiteLabelLogoRequest($uploadedLogo));
            }

            $organization->applyWhiteLabelUpdate(logo: $upload, name: $form->get('name')->getData());

            $manager->persist($organization);
            $manager->flush();

            return $this->redirect($adminUrlGenerator->setController(self::class)->setAction('index')->generateUrl());
        }

        return $this->render('admin/organizations/update_white_label.html.twig', [
            'organization' => $organization,
            'form' => $form->createView(),
        ]);
    }
}

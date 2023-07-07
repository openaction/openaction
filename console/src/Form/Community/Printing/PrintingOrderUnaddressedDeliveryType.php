<?php

namespace App\Form\Community\Printing;

use App\Entity\Community\PrintingOrder;
use App\Form\Community\Printing\Model\PrintingOrderUnaddressedDeliveryData;
use App\Form\CountryType;
use App\Platform\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintingOrderUnaddressedDeliveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var PrintingOrder $order */
        $order = $options['order'];

        $builder->add('quantities', PrintingOrderUnaddressedQuantitiesType::class, [
            'required' => true,
            'campaigns' => $order->getCampaigns(),
            'is_subrogated' => $order->isSubrogatedOrder(),
        ]);

        if ($order->isOfficialOrder()) {
            $containsOfficialPoster = false;
            $containsOtherOfficialProduct = false;

            foreach ($order->getProductsCodes() as $code) {
                if (Products::PRINT_OFFICIAL_BANNER === $code || Products::PRINT_OFFICIAL_POSTER === $code) {
                    $containsOfficialPoster = true;
                } else {
                    $containsOtherOfficialProduct = true;
                }
            }

            if ($containsOtherOfficialProduct) {
                $builder
                    ->add('addressName', TextType::class, ['required' => false])
                    ->add('addressStreet1', TextType::class, ['required' => false])
                    ->add('addressStreet2', TextType::class, ['required' => false])
                    ->add('addressZipCode', TextType::class, ['required' => false])
                    ->add('addressCity', TextType::class, ['required' => false])
                    ->add('addressCountry', CountryType::class, ['required' => false])
                    ->add('addressInstructions', TextareaType::class, ['required' => false])
                ;
            }

            if ($containsOfficialPoster) {
                $builder
                    ->add('posterAddressName', TextType::class, ['required' => false])
                    ->add('posterAddressStreet1', TextType::class, ['required' => false])
                    ->add('posterAddressStreet2', TextType::class, ['required' => false])
                    ->add('posterAddressZipCode', TextType::class, ['required' => false])
                    ->add('posterAddressCity', TextType::class, ['required' => false])
                    ->add('posterAddressCountry', CountryType::class, ['required' => false])
                    ->add('posterAddressInstructions', TextareaType::class, ['required' => false])
                ;
            }
        } else {
            $builder
                ->add('useMediapost', CheckboxType::class, ['required' => false])
                ->add('withEnveloping', CheckboxType::class, ['required' => false])
                ->add('addressName', TextType::class, ['required' => false])
                ->add('addressStreet1', TextType::class, ['required' => false])
                ->add('addressStreet2', TextType::class, ['required' => false])
                ->add('addressZipCode', TextType::class, ['required' => false])
                ->add('addressCity', TextType::class, ['required' => false])
                ->add('addressCountry', CountryType::class, ['required' => false])
                ->add('addressInstructions', TextareaType::class, ['required' => false])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('order');
        $resolver->setAllowedTypes('order', PrintingOrder::class);

        $resolver->setDefaults([
            'data_class' => PrintingOrderUnaddressedDeliveryData::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Util\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Video URL field transforming the provided URL into a "platform:id" string and vice-versa.
 */
class VideoUrlType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this);
    }

    public function transform($value): string
    {
        return Video::fromReference($value)?->toProviderUrl() ?? '';
    }

    public function reverseTransform($value): ?string
    {
        return Video::createFromUrl($value)?->toReference();
    }

    public function getParent(): string
    {
        return UrlType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'video_url';
    }
}

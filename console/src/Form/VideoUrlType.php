<?php

namespace App\Form;

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

    public function transform($identifier): string
    {
        if (!$identifier || !is_string($identifier)) {
            return '';
        }

        if (2 !== count($parts = explode(':', $identifier))) {
            return '';
        }

        if ('facebook' === $parts[0]) {
            return 'https://www.facebook.com/watch/?v='.$parts[1];
        }

        if ('youtube' === $parts[0]) {
            return 'https://www.youtube.com/watch?v='.$parts[1];
        }

        return '';
    }

    public function reverseTransform($videoUrl): ?string
    {
        if (!$videoUrl || !is_string($videoUrl)) {
            return null;
        }

        // youtube.com long URL
        if (str_contains($videoUrl, 'youtube.com')) {
            $params = [];
            parse_str(parse_url($videoUrl, PHP_URL_QUERY), $params);

            return !empty($params['v']) ? 'youtube:'.$params['v'] : null;
        }

        // youtu.be short URL
        if (str_contains($videoUrl, 'youtu.be')) {
            $id = trim(parse_url($videoUrl, PHP_URL_PATH), '/');

            return $id ? 'youtube:'.$id : null;
        }

        // facebook.com long URL
        if (str_contains($videoUrl, 'facebook.com')) {
            $params = [];
            parse_str(parse_url($videoUrl, PHP_URL_QUERY), $params);

            return !empty($params['v']) ? 'facebook:'.$params['v'] : null;
        }

        // fb.watch short URL
        if (str_contains($videoUrl, 'fb.watch')) {
            $id = trim(parse_url($videoUrl, PHP_URL_PATH), '/');

            return $id ? 'facebook:'.$id : null;
        }

        return null;
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

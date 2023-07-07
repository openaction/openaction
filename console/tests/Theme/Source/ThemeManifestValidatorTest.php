<?php

namespace App\Tests\Theme\Source;

use App\Tests\KernelTestCase;
use App\Theme\Source\ThemeManifestValidator;

class ThemeManifestValidatorTest extends KernelTestCase
{
    public function provideValidate()
    {
        yield [
            'payload' => [],
            'expected' => [
                'name: The property name is required',
                'description: The property description is required',
                'thumbnail: The property thumbnail is required',
                'templates: The property templates is required',
            ],
        ];

        yield [
            'payload' => [
                'name' => 1,
                'assets' => 1,
                'defaultColors' => 1,
                'defaultFonts' => 1,
            ],
            'expected' => [
                'description: The property description is required',
                'thumbnail: The property thumbnail is required',
                'templates: The property templates is required',
                'name: Integer value found, but an object is required',
                'defaultColors: Integer value found, but an object is required',
                'defaultFonts: Integer value found, but an object is required',
                'assets: Integer value found, but an array is required',
            ],
        ];

        yield [
            'payload' => [
                'name' => ['en' => 'B'],
                'description' => ['fr' => 'A'],
                'thumbnail' => '',
                'templates' => [
                    'style' => '',
                    'script' => '',
                    'head' => '',
                    'layout' => '',
                    'header' => '',
                    'footer' => '',
                    'list' => '',
                    'content' => '',
                    'home-calls-to-action' => '',
                    'home-custom-content' => '',
                    'home-newsletter' => '',
                    'home-posts' => '',
                    'home-events' => '',
                    'home-socials' => '',
                    'manifesto-list' => '',
                    'manifesto-view' => '',
                    'trombinoscope-list' => '',
                    'trombinoscope-view' => '',
                ],
                'assets' => [],
            ],
            'expected' => [
                'name.fr: The property fr is required',
                'description.en: The property en is required',
                'templates.home: The property home is required',
            ],
        ];

        yield [
            'payload' => [
                'name' => ['fr' => 'A', 'en' => 'B'],
                'defaultColors' => ['primary' => '000', 'secondary' => '111', 'third' => '222'],
                'defaultFonts' => ['title' => 'Roboto', 'text' => 'Roboto'],
                'description' => ['fr' => 'A', 'en' => 'B'],
                'thumbnail' => '',
                'templates' => [
                    'style' => '',
                    'script' => '',
                    'head' => '',
                    'layout' => '',
                    'header' => '',
                    'footer' => '',
                    'list' => '',
                    'content' => '',
                    'home' => '',
                    'home-calls-to-action' => '',
                    'home-custom-content' => '',
                    'home-newsletter' => '',
                    'home-posts' => '',
                    'home-events' => '',
                    'home-socials' => '',
                    'manifesto-list' => '',
                    'manifesto-view' => '',
                    'trombinoscope-list' => '',
                    'trombinoscope-view' => '',
                ],
                'assets' => [],
            ],
            'expected' => [],
        ];
    }

    /**
     * @dataProvider provideValidate
     */
    public function testValidate(array $payload, array $expected)
    {
        /** @var ThemeManifestValidator $validator */
        $validator = static::getContainer()->get(ThemeManifestValidator::class);

        $this->assertSame($expected, $validator->validate($payload));
    }
}

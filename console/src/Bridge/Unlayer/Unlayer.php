<?php

namespace App\Bridge\Unlayer;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Unlayer implements UnlayerInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private string $apiKey,
    ) {
    }

    public function getEmailingTemplates(): array
    {
        // Handle API errror gracefully to still allow to create empty templates if API is unavailable
        try {
            return $this->getTemplates('email', 'emailing');
        } catch (\Exception $e) {
            $this->logger->error('Unlayer API request failed', ['exception' => $e]);

            return [152724 => $this->getEmptyTemplate('emailing')];
        }
    }

    public function getAutomationTemplates(): array
    {
        // Handle API errror gracefully to still allow to create empty templates if API is unavailable
        try {
            return $this->getTemplates('email', 'automation');
        } catch (\Exception $e) {
            $this->logger->error('Unlayer API request failed', ['exception' => $e]);

            return [152724 => $this->getEmptyTemplate('automation')];
        }
    }

    private function getTemplates(string $displayMode, string $role): array
    {
        // Handle local development and tests
        if (!$this->apiKey) {
            return [152724 => $this->getEmptyTemplate($role)];
        }

        $response = $this->client->request('GET', 'https://api.unlayer.com/v2/templates', [
            'auth_basic' => [$this->apiKey, ''],
        ]);

        // Sort by name
        $registry = [];
        $ids = [];
        $names = [];

        foreach ($response->toArray()['data'] ?? [] as $item) {
            if ($displayMode !== $item['displayMode']) {
                continue;
            }

            if (!str_starts_with($item['name'], $role.'.')) {
                continue;
            }

            $registry[$item['id']] = $item;
            $ids[] = $item['id'];
            $names[] = $item['name'];
        }

        array_multisort($names, SORT_ASC, $ids);

        // Build result
        $templates = [];
        foreach ($ids as $id) {
            $templates[$id] = $registry[$id];
        }

        return $templates;
    }

    private function getEmptyTemplate(string $role): array
    {
        return [
            'id' => 152724,
            'name' => $role.'.1.empty',
            'design' => [
                'body' => [
                    'id' => 'a3j74EQuoC',
                    'rows' => [],
                    'values' => [
                        '_meta' => [
                            'htmlID' => 'u_body',
                            'htmlClassNames' => 'u_body',
                        ],
                        'linkStyle' => [
                            'body' => true,
                            'inherit' => false,
                            'linkColor' => '#0077cc',
                            'linkUnderline' => true,
                            'linkHoverColor' => '#0000ee',
                            'linkHoverUnderline' => true,
                        ],
                        'textColor' => '#000000',
                        'fontFamily' => [
                            'label' => 'Arial',
                            'value' => 'arial,helvetica,sans-serif',
                        ],
                        'popupWidth' => '600px',
                        'popupHeight' => 'auto',
                        'borderRadius' => '10px',
                        'contentAlign' => 'center',
                        'contentWidth' => '500px',
                        'popupPosition' => 'center',
                        'preheaderText' => '',
                        'backgroundColor' => '#ffffff',
                        'backgroundImage' => [
                            'url' => '',
                            'cover' => false,
                            'center' => true,
                            'repeat' => false,
                            'fullWidth' => true,
                        ],
                        'contentVerticalAlign' => 'center',
                        'popupBackgroundColor' => '#FFFFFF',
                        'popupBackgroundImage' => [
                            'url' => '',
                            'cover' => true,
                            'center' => true,
                            'repeat' => false,
                            'fullWidth' => true,
                        ],
                        'popupCloseButton_action' => [
                            'name' => 'close_popup',
                            'attrs' => [
                                'onClick' => 'document.querySelector(\'.u-popup-container\').style.display = \'none\';',
                            ],
                        ],
                        'popupCloseButton_margin' => '0px',
                        'popupCloseButton_position' => 'top-right',
                        'popupCloseButton_iconColor' => '#000000',
                        'popupOverlay_backgroundColor' => 'rgba(0, 0, 0, 0.1)',
                        'popupCloseButton_borderRadius' => '0px',
                        'popupCloseButton_backgroundColor' => '#DDDDDD',
                    ],
                ],
                'counters' => [
                    'u_row' => 1,
                    'u_column' => 1,
                    'u_content_image' => 1,
                    'u_content_divider' => 1,
                    'u_content_heading' => 2,
                ],
                'schemaVersion' => 8,
            ],
            'displayMode' => 'email',
            'createdAt' => '2022-03-30T13:28:11.023Z',
            'updatedAt' => '2022-04-03T15:43:29.380Z',
        ];
    }
}

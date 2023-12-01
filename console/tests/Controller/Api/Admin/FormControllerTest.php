<?php

namespace App\Tests\Controller\Api\Admin;

use App\Tests\ApiTestCase;

class FormControllerTest extends ApiTestCase
{
    public function testListAnswers()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/admin/forms/4x0UjLrg8RJYHWTY9ZyCWs/answers', self::ACME_ADMIN_TOKEN);

        // Test the payload
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'FormAnswer',
                    '_links' => [
                        'form' => 'http://localhost/api/website/forms/4x0UjLrg8RJYHWTY9ZyCWs',
                    ],
                    'id' => '55WbpdJX0sDcKmUWMp67mJ',
                    'contactId' => '3aKCEDnsBNA8PYe6xqkO9u',
                    'answers' => [
                        'Titre de votre proposition' => 'Revitaliser le centre urbain',
                        'Quels sont les sujets sur lesquels porte votre proposition ?' => 'Travail',
                        'Quelles sont vos idées ?' => 'Les centres urbains sont de plus en plus désertés.',
                        'Votre prénom' => 'Titouan',
                        'Votre nom' => 'Galopin',
                        'Votre email' => 'titouan.galopin@citipo.com',
                        'Votre pays' => 'France',
                        'Votre genre' => 'Autre',
                        'Votre date de naissance' => '',
                        'A quelle heure souhaitez-vous être rencontacté(e) ?' => '10:00:00',
                    ],
                ],
            ],
            'meta' => [
                'pagination' => [
                    'total' => 1,
                    'count' => 1,
                    'per_page' => 50,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'links' => [],
                ],
            ],
        ]);
    }
}

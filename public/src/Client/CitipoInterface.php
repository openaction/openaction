<?php

namespace App\Client;

use App\Client\Model\ApiCollection;
use App\Client\Model\ApiResource;
use Symfony\Component\HttpFoundation\File\File;

interface CitipoInterface
{
    public function getProject(string $apiToken): ?ApiResource;

    public function getProjectSitemap(string $apiToken): ?ApiResource;

    public function getPagesCategories(string $apiToken): ApiCollection;

    public function getPage(string $apiToken, string $id): ?ApiResource;

    public function getPosts(string $apiToken, int $page, string $category = null, string $author = null): ApiCollection;

    public function getPostsCategories(string $apiToken): ApiCollection;

    public function getPost(string $apiToken, string $id): ?ApiResource;

    public function getEvents(string $apiToken, int $page, string $category = null, bool $archived = false): ApiCollection;

    public function getEventsCategories(string $apiToken): ApiCollection;

    public function getEvent(string $apiToken, string $id): ?ApiResource;

    public function getTrombinoscope(string $apiToken): ApiCollection;

    public function getTrombinoscopePerson(string $apiToken, string $id): ?ApiResource;

    public function getManifesto(string $apiToken): ApiCollection;

    public function getManifestoTopic(string $apiToken, string $id): ?ApiResource;

    public function getDocument(string $apiToken, string $id): ?ApiResource;

    public function getForm(string $apiToken, string $id): ?ApiResource;

    public function validateArea(string $apiToken, string $country, string $zipCode): ApiResource;

    public function persistContact(string $apiToken, array $payload): ApiResource;

    public function persistContactPicture(string $apiToken, string $id, File $picture): ApiResource;

    public function getContact(string $apiToken, string $id): ?ApiResource;

    public function getContactStatus(string $apiToken, string $email): ApiResource;

    public function createFormAnswer(string $apiToken, string $id, array $payload): ApiResource;

    public function confirmAccount(string $apiToken, string $id, string $token): ?ApiResource;

    public function requestReset(string $apiToken, string $id): ?ApiResource;

    public function confirmReset(string $apiToken, string $id, string $token, string $password): ?ApiResource;

    public function requestEmailUpdate(string $apiToken, string $id, string $newEmail): ?ApiResource;

    public function confirmEmailUpdate(string $apiToken, string $id, string $token): ?ApiResource;

    public function requestUnregister(string $apiToken, string $id): ?ApiResource;

    public function confirmUnregister(string $apiToken, string $id, string $token): ?ApiResource;

    public function login(string $apiToken, string $email, string $password): ?ApiResource;

    public function authorize(string $apiToken, array $authToken): ?ApiResource;

    public function getMembersPages(string $apiToken, array $authToken, string $category = null): ApiCollection;

    public function getMembersPage(string $apiToken, array $authToken, string $id): ?ApiResource;

    public function getMembersPosts(string $apiToken, array $authToken, int $page, string $category = null): ApiCollection;

    public function getMembersPost(string $apiToken, array $authToken, string $id): ?ApiResource;

    public function getMembersEvents(string $apiToken, array $authToken, int $page, string $category = null): ApiCollection;

    public function getMembersEvent(string $apiToken, array $authToken, string $id): ?ApiResource;

    public function getMembersForms(string $apiToken, array $authToken, int $page): ApiCollection;

    public function getMembersForm(string $apiToken, array $authToken, string $id): ?ApiResource;

    public function getPhoningCampaign(string $apiToken, array $authToken, string $id): ?ApiResource;

    public function resolvePhoningCampaignTarget(string $apiToken, array $authToken, string $id): ?ApiResource;

    public function getPhoningCampaignCall(string $apiToken, array $authToken, string $id, string $callId): ?ApiResource;

    public function persistPhoningCampaignCallResult(string $apiToken, array $authToken, string $id, string $callId, string $status, array $answers = null);
}

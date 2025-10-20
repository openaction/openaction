<?php

namespace App\Client;

use App\Client\Model\ApiCollection;
use App\Client\Model\ApiResource;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Citipo implements CitipoInterface
{
    public const AUTH_TOKEN_HEADER = 'X-Citipo-Auth-Token';

    private HttpClientInterface $httpClient;
    private RequestStack $requestStack;

    public function __construct(HttpClientInterface $citipo, RequestStack $requestStack)
    {
        $this->httpClient = $citipo;
        $this->requestStack = $requestStack;
    }

    public function getProject(string $apiToken): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/project?includes=header,footer,pages,posts,home', $apiToken, nullable: false));
    }

    public function getProjectSitemap(string $apiToken): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/project/sitemap', $apiToken));
    }

    public function getPagesCategories(string $apiToken): ApiCollection
    {
        return $this->parseCollection($this->request('GET', '/api/website/pages-categories', $apiToken));
    }

    public function getPage(string $apiToken, string $id): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/website/pages/'.$id, $apiToken));
    }

    public function getPostsCategories(string $apiToken): ApiCollection
    {
        return $this->parseCollection($this->request('GET', '/api/website/posts-categories', $apiToken));
    }

    public function getPosts(string $apiToken, int $page, ?string $category = null, ?string $author = null): ApiCollection
    {
        return $this->parseCollection($this->request('GET', '/api/website/posts?page='.$page.'&category='.$category.'&author='.$author, $apiToken));
    }

    public function getPost(string $apiToken, string $id): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/website/posts/'.$id.'?includes=more', $apiToken));
    }

    public function getEventsCategories(string $apiToken): ApiCollection
    {
        return $this->parseCollection($this->request('GET', '/api/website/events-categories', $apiToken));
    }

    public function getEvents(string $apiToken, int $page, ?string $category = null, ?string $participant = null, bool $archived = false): ApiCollection
    {
        return $this->parseCollection($this->request('GET', '/api/website/events?page='.$page.'&category='.$category.'&participant='.$participant.'&archived='.($archived ? '1' : '0'), $apiToken));
    }

    public function getEvent(string $apiToken, string $id): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/website/events/'.$id, $apiToken));
    }

    public function getTrombinoscopeCategories(string $apiToken): ApiCollection
    {
        return $this->parseCollection($this->request('GET', '/api/website/trombinoscope-categories', $apiToken));
    }

    public function getTrombinoscope(string $apiToken, ?string $category = null): ApiCollection
    {
        return $this->parseCollection($this->request('GET', '/api/website/trombinoscope?category='.$category, $apiToken));
    }

    public function getTrombinoscopePerson(string $apiToken, string $id): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/website/trombinoscope/'.$id.'?includes=previous,next,posts,categories', $apiToken));
    }

    public function getManifesto(string $apiToken): ApiCollection
    {
        return $this->parseCollection($this->request('GET', '/api/website/manifesto', $apiToken));
    }

    public function getManifestoTopic(string $apiToken, string $id): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/website/manifesto/'.$id.'?includes=previous,next', $apiToken));
    }

    public function getDocument(string $apiToken, string $id): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/website/documents/'.$id, $apiToken));
    }

    public function persistContact(string $apiToken, array $payload): ApiResource
    {
        return $this->parseResource(
            $this->request(
                'POST',
                '/api/community/contacts',
                $apiToken,
                ['json' => $payload],
                nullable: false,
            )
        );
    }

    public function persistContactPicture(string $apiToken, string $id, File $picture): ApiResource
    {
        $formData = new FormDataPart(['picture' => DataPart::fromPath($picture->getPathname())]);

        return $this->parseResource(
            $this->request('POST', '/api/community/contacts/'.$id.'/picture', $apiToken, [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ], nullable: false)
        );
    }

    public function validateArea(string $apiToken, string $country, string $zipCode): ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/areas/validate/'.$country.'/'.$zipCode, $apiToken));
    }

    public function getContact(string $apiToken, string $id): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/community/contacts/'.$id, $apiToken));
    }

    public function getContactStatus(string $apiToken, string $email): ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/community/contacts/status/'.$email, $apiToken));
    }

    public function getForm(string $apiToken, string $id): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/website/forms/'.$id, $apiToken));
    }

    public function getPetitions(string $apiToken, int $page, ?string $category = null): ApiCollection
    {
        return $this->parseCollection($this->request('GET', '/api/website/petitions?page='.$page.'&category='.$category, $apiToken));
    }

    public function getPetition(string $apiToken, string $slug): ?ApiResource
    {
        return $this->parseResource($this->request('GET', '/api/website/petitions/'.$slug, $apiToken));
    }

    public function createFormAnswer(string $apiToken, string $id, array $payload, ?array $authToken = null): ApiResource
    {
        if ($authToken) {
            return $this->parseResource(
                $this->memberRequest('POST', '/api/website/forms/'.$id.'/answer', $apiToken, $authToken, [
                    'json' => ['fields' => $payload],
                ], nullable: false)
            );
        }

        return $this->parseResource(
            $this->request('POST', '/api/website/forms/'.$id.'/answer', $apiToken, [
                'json' => ['fields' => $payload],
            ], nullable: false)
        );
    }

    public function confirmAccount(string $apiToken, string $id, string $token): ?ApiResource
    {
        $endpoint = '/api/community/members/register/confirm/'.$id.'/'.$token;
        $response = $this->httpClient->request('POST', $endpoint, ['auth_bearer' => $apiToken]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        return $this->parseResource($response->toArray());
    }

    public function requestReset(string $apiToken, string $id): ?ApiResource
    {
        return $this->parseResource($this->request('POST', '/api/community/members/reset/request/'.$id, $apiToken, nullable: false));
    }

    public function confirmReset(string $apiToken, string $id, string $token, string $password): ?ApiResource
    {
        $endpoint = '/api/community/members/reset/confirm/'.$id.'/'.$token;
        $response = $this->httpClient->request('POST', $endpoint, [
            'auth_bearer' => $apiToken,
            'json' => ['password' => $password],
        ]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        return $this->parseResource($response->toArray());
    }

    public function requestEmailUpdate(string $apiToken, string $id, string $newEmail): ?ApiResource
    {
        $response = $this->httpClient->request('POST', '/api/community/contacts/'.$id.'/email', [
            'auth_bearer' => $apiToken,
            'json' => ['newEmail' => $newEmail],
        ]);

        return $this->parseResource($response->toArray());
    }

    public function confirmEmailUpdate(string $apiToken, string $id, string $token): ?ApiResource
    {
        $response = $this->httpClient->request('POST', '/api/community/contacts/confirm/'.$id.'/'.$token.'/email', [
            'auth_bearer' => $apiToken,
        ]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        return $this->parseResource($response->toArray());
    }

    public function requestUnregister(string $apiToken, string $id): ?ApiResource
    {
        $response = $this->httpClient->request('POST', '/api/community/contacts/'.$id.'/unregister', [
            'auth_bearer' => $apiToken,
        ]);

        return $this->parseResource($response->toArray());
    }

    public function confirmUnregister(string $apiToken, string $id, string $token): ?ApiResource
    {
        $endpoint = '/api/community/contacts/confirm/'.$id.'/'.$token.'/unregister';
        $response = $this->httpClient->request('POST', $endpoint, ['auth_bearer' => $apiToken]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        return $this->parseResource($response->toArray());
    }

    public function login(string $apiToken, string $email, string $password): ?ApiResource
    {
        $response = $this->httpClient->request('POST', '/api/community/members/login', [
            'auth_bearer' => $apiToken,
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        return $this->parseResource($response->toArray());
    }

    public function authorize(string $apiToken, array $authToken): ?ApiResource
    {
        $response = $this->httpClient->request('POST', '/api/community/members/authorize', [
            'auth_bearer' => $apiToken,
            'headers' => [
                self::AUTH_TOKEN_HEADER => json_encode($authToken, JSON_THROW_ON_ERROR),
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        return $this->parseResource($response->toArray());
    }

    public function getMembersPages(string $apiToken, array $authToken, ?string $category = null): ApiCollection
    {
        return $this->parseCollection(
            $this->memberRequest('GET', '/api/community/area/pages?category='.$category, $apiToken, $authToken, [], false)
        );
    }

    public function getMembersPage(string $apiToken, array $authToken, string $id): ?ApiResource
    {
        return $this->parseResource(
            $this->memberRequest('GET', '/api/community/area/pages/'.$id, $apiToken, $authToken)
        );
    }

    public function getMembersPosts(string $apiToken, array $authToken, int $page, ?string $category = null): ApiCollection
    {
        return $this->parseCollection(
            $this->memberRequest('GET', '/api/community/area/posts?page='.$page.'&category='.$category, $apiToken, $authToken, [], false)
        );
    }

    public function getMembersPost(string $apiToken, array $authToken, string $id): ?ApiResource
    {
        return $this->parseResource(
            $this->memberRequest('GET', '/api/community/area/posts/'.$id.'?includes=more', $apiToken, $authToken)
        );
    }

    public function getMembersEvents(string $apiToken, array $authToken, int $page, ?string $category = null): ApiCollection
    {
        return $this->parseCollection(
            $this->memberRequest('GET', '/api/community/area/events?page='.$page.'&category='.$category, $apiToken, $authToken, [], false)
        );
    }

    public function getMembersEvent(string $apiToken, array $authToken, string $id): ?ApiResource
    {
        return $this->parseResource(
            $this->memberRequest('GET', '/api/community/area/events/'.$id, $apiToken, $authToken)
        );
    }

    public function getMembersForms(string $apiToken, array $authToken, int $page): ApiCollection
    {
        return $this->parseCollection(
            $this->memberRequest('GET', '/api/community/area/forms?page='.$page, $apiToken, $authToken, [], false)
        );
    }

    public function getMembersForm(string $apiToken, array $authToken, string $id): ?ApiResource
    {
        return $this->parseResource(
            $this->memberRequest('GET', '/api/community/area/forms/'.$id, $apiToken, $authToken)
        );
    }

    public function getPhoningCampaign(string $apiToken, array $authToken, string $id): ?ApiResource
    {
        return $this->parseResource(
            $this->memberRequest('GET', '/api/community/area/phoning/'.$id, $apiToken, $authToken)
        );
    }

    public function resolvePhoningCampaignTarget(string $apiToken, array $authToken, string $id): ?ApiResource
    {
        return $this->parseResource(
            $this->memberRequest('POST', '/api/community/area/phoning/'.$id.'/resolve-target', $apiToken, $authToken, nullable: false)
        );
    }

    public function getPhoningCampaignCall(string $apiToken, array $authToken, string $id, string $callId): ?ApiResource
    {
        return $this->parseResource(
            $this->memberRequest('GET', '/api/community/area/phoning/'.$id.'/call/'.$callId, $apiToken, $authToken, [], false)
        );
    }

    public function persistPhoningCampaignCallResult(string $apiToken, array $authToken, string $id, string $callId, string $status, ?array $answers = null)
    {
        return $this->parseResource(
            $this->memberRequest('POST', '/api/community/area/phoning/'.$id.'/call/'.$callId.'/save', $apiToken, $authToken, [
                'json' => [
                    'status' => $status,
                    'answers' => $answers ?: [],
                ],
            ], nullable: false)
        );
    }

    private function memberRequest(string $method, string $url, string $apiToken, array $authToken, array $options = [], bool $nullable = true): ?array
    {
        $options['auth_bearer'] = $apiToken;
        $options['headers'][self::AUTH_TOKEN_HEADER] = json_encode($authToken, JSON_THROW_ON_ERROR);

        return $this->request($method, $url, $apiToken, $options, $nullable);
    }

    private function request(string $method, string $url, string $apiToken, array $options = [], bool $nullable = true): ?array
    {
        $ray = null;
        if ($currentRequest = $this->requestStack->getCurrentRequest()) {
            $ray = $currentRequest->headers->get('CF-Ray');
        }

        $options['auth_bearer'] = $apiToken;
        $options['headers']['X-Parent-Ray'] = $ray;

        $response = $this->httpClient->request($method, $url, $options);

        if ($response->getStatusCode() >= 300) {
            if ($nullable) {
                return null;
            }

            throw new \RuntimeException(sprintf("Citipo API request failed:\nHTTP %s\n%s", $response->getStatusCode(), substr($response->getContent(false), 0, 200)));
        }

        return $response->toArray();
    }

    private function parseCollection(array $data): ApiCollection
    {
        $collection = new ApiCollection();
        $collection->meta = $data['meta'] ?? [];

        foreach ($data['data'] ?? [] as $key => $item) {
            $collection->data[$key] = $this->parseResource($item);
        }

        return $collection;
    }

    private function parseResource(?array $data): ?ApiResource
    {
        if (null === $data) {
            return null;
        }

        $resource = new ApiResource();

        foreach ($data as $key => $value) {
            if ('_resource' === $key) {
                $resource->type = $value;

                continue;
            }

            if ('_links' === $key) {
                foreach ($value as $name => $link) {
                    $resource->links[$name] = $link;
                }

                continue;
            }

            if (is_array($value)) {
                if (isset($value['data'])) {
                    $resource->{$key} = $this->parseCollection($value);

                    continue;
                }

                if (isset($value['_resource'])) {
                    $resource->{$key} = $this->parseResource($value);

                    continue;
                }
            }

            $resource->{$key} = $value;
        }

        return $resource;
    }
}

<?php

namespace App\Controller;

use App\Client\CitipoInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DocumentController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    /**
     * @Route("/document/{id}/{name}", name="document_serve")
     */
    public function serve(HttpClientInterface $httpClient, string $id, string $name)
    {
        $this->denyUnlessToolEnabled('website_documents');

        $document = $this->citipo->getDocument($this->getApiToken(), $id);

        if (!$document) {
            throw $this->createNotFoundException();
        }

        if ($document->name !== $name) {
            return $this->redirectToRoute('document_serve', ['id' => $id, 'name' => $document->name], Response::HTTP_MOVED_PERMANENTLY);
        }

        $file = $httpClient->request('GET', str_replace('localhost', 'console', $document->file));

        if (404 === $file->getStatusCode()) {
            throw $this->createNotFoundException();
        }

        $extension = pathinfo($document->name, PATHINFO_EXTENSION);

        // PDF: inline
        if ('pdf' === $extension) {
            $response = new Response($file->getContent());
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set(
                'Content-Disposition',
                HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_INLINE, $document->name, 'document.pdf')
            );

            return $this->httpCache(60, $response);
        }

        // Others: download
        $response = new Response($file->getContent());
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set(
            'Content-Disposition',
            HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $document->name, 'document.'.$extension)
        );

        return $this->httpCache(60, $response);
    }
}

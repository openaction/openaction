<?php

namespace App\Billing\Invoice\Generator;

use App\Entity\Billing\Order;
use App\Entity\Billing\Quote;
use App\Platform\Companies;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

class PdfGenerator
{
    private string $gotenbergEndpoint;
    private HttpClientInterface $httpClient;
    private Environment $twig;

    public function __construct(string $gotenbergEndpoint, HttpClientInterface $h, Environment $t)
    {
        $this->gotenbergEndpoint = $gotenbergEndpoint;
        $this->httpClient = $h;
        $this->twig = $t;
    }

    public function generateInvoice(Order $order): string
    {
        // Generate the invoice content
        $html = $this->twig->render('billing/invoice.html.twig', [
            'invoice' => $order,
            'company' => Companies::BILLING[$order->getCompany()],
        ]);

        // Render using Gotenberg
        $formData = new FormDataPart(['index.html' => new DataPart($html, 'index.html', 'text/html')]);

        $response = $this->httpClient->request('POST', $this->gotenbergEndpoint.'/forms/chromium/convert/html', [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable(),
        ]);

        return $response->getContent();
    }

    public function generateQuote(Quote $quote): string
    {
        // Generate the invoice content
        $html = $this->twig->render('billing/quote.html.twig', [
            'quote' => $quote,
            'company' => Companies::BILLING[$quote->getCompany()],
        ]);

        // Render using Gotenberg
        $formData = new FormDataPart(['index.html' => new DataPart($html, 'index.html', 'text/html')]);

        $response = $this->httpClient->request('POST', $this->gotenbergEndpoint.'/forms/chromium/convert/html', [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable(),
        ]);

        return $response->getContent();
    }
}

<?php

namespace App\Sitemap;

class Sitemap
{
    public const MAX_URLS = 50000;

    private string $xmlVersion = '1.0';
    private string $xmlEncoding = 'UTF-8';
    private string $xmlNamespaceUri = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    private \DOMDocument $document;
    private \DOMElement $rootNode;
    private bool $isFrozen = false;
    private int $urlCount = 0;

    /**
     * Sets up the sitemap XML document and urlset node.
     */
    public function __construct()
    {
        $this->document = new \DOMDocument($this->xmlVersion, $this->xmlEncoding);
        $this->rootNode = $this->document->createElementNS($this->xmlNamespaceUri, 'urlset');

        // Make the output Pretty
        $this->document->formatOutput = true;
    }

    /**
     * Adds the URL to the urlset.
     */
    public function add(string $loc, ?string $lastmod = null, ?string $changefreq = null, ?float $priority = null): self
    {
        $loc = $this->escapeString($loc);
        $lastmod = !is_null($lastmod) ? $this->formatDate($lastmod) : null;

        return $this->addUrlToDocument(compact('loc', 'lastmod', 'changefreq', 'priority'));
    }

    /**
     * Freeze the sitemap, and append the rootNode to the document.
     */
    public function freeze()
    {
        $this->document->appendChild($this->rootNode);
        $this->isFrozen = true;
    }

    public function isFrozen(): bool
    {
        return $this->isFrozen;
    }

    /**
     * Gets the number of Urls in the sitemap.
     */
    public function getUrlCount(): int
    {
        return $this->urlCount;
    }

    /**
     * Checks if the sitemap contains the maximum URL count.
     */
    public function hasMaxUrlCount(): bool
    {
        return $this->urlCount === static::MAX_URLS;
    }

    /**
     * Converts the Sitemap to an XML string.
     */
    public function toString(): string
    {
        return (string) $this;
    }

    /**
     * Converts the Sitemap to an XML string.
     */
    public function __toString(): string
    {
        if (!$this->isFrozen()) {
            $this->freeze();
        }

        return $this->document->saveXML();
    }

    /**
     * Adds a URL to the document with the given array of elements.
     */
    private function addUrlToDocument(array $urlArray): self
    {
        if ($this->hasMaxUrlCount()) {
            throw new \LogicException('Maximum number of URLs has been reached, cannot add more.');
        }

        $node = $this->document->createElement('url');

        foreach ($urlArray as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            $node->appendChild(new \DOMElement($key, $value));
        }

        $this->rootNode->appendChild($node);
        ++$this->urlCount;

        return $this;
    }

    /**
     * Escapes a string so it can be inserted into the Sitemap.
     */
    private function escapeString(string $string): string
    {
        $from = ['&', '\'', '"', '>', '<'];
        $to = ['&amp;', '&apos;', '&quot;', '&gt;', '&lt;'];

        return str_replace($from, $to, $string);
    }

    /**
     * Takes a date as a string (or int in the case of a unix timestamp).
     */
    private function formatDate(string $dateString): string
    {
        try {
            // We have to handle timestamps a little differently
            if (is_numeric($dateString) && (int) $dateString == $dateString) {
                $date = \DateTime::createFromFormat('U', (int) $dateString, new \DateTimeZone('UTC'));
            } else {
                $date = new \DateTime($dateString, new \DateTimeZone('UTC'));
            }

            return $date->format(\DateTime::W3C);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Malformed last modified date: {$dateString}", 0, $e);
        }
    }
}

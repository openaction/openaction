<?php

namespace App\Bridge\Sendgrid;

use SendGrid\Mail\Mail;
use SendGrid\Response;

interface SendgridInterface
{
    public function sendMessage(Mail $mail): Response;

    public function verifySignature(string $content, string $signature, string $timestamp): bool;

    public function findDomainsByName(string $domain): array;

    public function removeDomain(int $id): void;

    public function createDomain(string $domain): int;

    public function findBrandedLinksByName(string $domain): array;

    public function removeBrandedLink(int $id): void;

    public function createBrandedLink(string $domain): int;

    public function getDomainConfig(int $domainAuthId): array;

    public function getBrandedLinkConfig(int $brandedLinkId): array;

    public function requestDomainConfigValidation(int $domainAuthId): void;

    public function requestBrandedLinkConfigValidation(int $brandedLinkId): void;
}

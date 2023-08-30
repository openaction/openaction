<?php

namespace App\Bridge\Uploadcare;

use App\Bridge\Uploadcare\Model\UploadKey;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Uploadcare implements UploadcareInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $publicKey,
        private string $secretKey,
    ) {
    }

    public function downloadFile(string $uuid, string $extension): ?File
    {
        $response = $this->httpClient->request('GET', 'https://ucarecdn.com/'.$uuid.'/');
        if (200 !== $response->getStatusCode()) {
            return null;
        }

        $filename = sys_get_temp_dir().'/'.$uuid.'.'.$extension;
        file_put_contents($filename, $response->getContent());

        return new File($filename);
    }

    public function generateUploadKey(): UploadKey
    {
        // Expires after 15 minutes
        return new UploadKey($this->publicKey, hash_hmac('sha256', time() + 900, $this->secretKey), time() + 900);
    }
}

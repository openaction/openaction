<?php

namespace App\Bridge\Uploadcare;

use App\Bridge\Uploadcare\Model\UploadKey;
use Symfony\Component\HttpFoundation\File\File;

class MockUploadcare implements UploadcareInterface
{
    private const MOCK_FILE = __DIR__.'/../../../var/cache/test/mock_uploadercare_file';
    private const DEFAULT_FILE = __DIR__.'/../../../tests/Fixtures/printing/official_poster.pdf';

    public static function setMockFile(string $filename)
    {
        file_put_contents(self::MOCK_FILE, file_get_contents($filename));
    }

    public function downloadFile(string $uuid, string $extension): ?File
    {
        if (!file_exists(self::MOCK_FILE)) {
            self::setMockFile(self::DEFAULT_FILE);
        }

        $filename = sys_get_temp_dir().'/'.$uuid.'.'.$extension;
        file_put_contents($filename, file_get_contents(self::MOCK_FILE));

        return new File($filename);
    }

    public function generateUploadKey(): UploadKey
    {
        // Expires after 15 minutes
        return new UploadKey('public', hash_hmac('sha256', time() + 900, 'secret'), time() + 900);
    }
}

<?php

namespace App\Controller\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ContentEditorUploadControllerTrait
{
    private function createContentEditorUploadedFile(Request $request): UploadedFile
    {
        $count = $request->query->getInt('count');

        $tempName = @tempnam(md5(uniqid('', true)), 'contenteditor_');
        file_put_contents($tempName, base64_decode($request->request->get('hidimg-'.$count)));

        $originalName = $request->request->get('hidname-'.$count).'.'.$request->request->get('hidtype-'.$count);

        $file = new UploadedFile($tempName, $originalName);

        if ($file->getSize() > 26214400) { // 25 MiB
            throw new BadRequestHttpException('File too large.');
        }

        if (!str_starts_with($file->getMimeType(), 'image/')) {
            throw new BadRequestHttpException('Invalid image.');
        }

        return $file;
    }

    private function createContentEditorUploadResponse(int $count, string $newUrl): Response
    {
        return new Response('
            <html lang="en">
                <body onload="
                    parent.document.getElementById(\'img-'.$count.'\').setAttribute(\'src\', \''.$newUrl.'\'); 
                    parent.document.getElementById(\'img-'.$count.'\').removeAttribute(\'id\');
                "></body>
            </html>
        ');
    }
}

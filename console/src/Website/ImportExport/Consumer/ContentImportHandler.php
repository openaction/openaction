<?php

namespace App\Website\ImportExport\Consumer;

use App\Repository\Community\ContentImportRepository;
use App\Website\ImportExport\Parser\ExternalContentParserInterface;
use League\Flysystem\FilesystemReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ContentImportHandler
{
    /**
     * @param iterable<ExternalContentParserInterface> $contentParsers
     */
    public function __construct(
        private readonly ContentImportRepository $contentImportRepository,
        private readonly FilesystemReader $cdnStorage,
        private readonly LoggerInterface $logger,

        #[TaggedIterator('app.external_content_parser')]
        private readonly iterable $contentParsers,
    ) {
    }

    public function __invoke(ContentImportMessage $message): bool
    {
        if (!$import = $this->contentImportRepository->find($message->getImportId())) {
            $this->logger->error('Import not found by its ID', ['id' => $message->getImportId()]);

            return true;
        }

        if ($import->getJob()->isFinished()) {
            $this->logger->error('Import finished', ['id' => $message->getImportId()]);

            return true;
        }

        foreach ($this->contentParsers as $contentParser) {
            if ($contentParser->getSupportedSource() === $import->getSource()) {
                $localFile = sys_get_temp_dir().'/citipo-content-import-'.$import->getId().'.'.$import->getFile()->getExtension();
                file_put_contents($localFile, $this->cdnStorage->readStream($import->getFile()->getPathname()));

                $this->logger->info('Importing', [
                    'id' => $import->getId(),
                    'filename' => $localFile,
                    'parser' => $contentParser::class,
                ]);

                try {
                    $contentParser->import($import, $localFile);
                } finally {
                    @unlink($localFile);
                }

                return true;
            }
        }

        throw new \RuntimeException('No content parser support import '.$import->getId());
    }
}

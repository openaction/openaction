<?php

namespace App\Community\ImportExport;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Community\ImportExport\Consumer\ImportMessage;
use App\Entity\Community\Import;
use App\Entity\Community\Model\ImportHead;
use App\Entity\Organization;
use App\Form\Community\Model\ImportMetadataData;
use App\Repository\AreaRepository;
use App\Util\Spreadsheet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ContactImporter
{
    private EntityManagerInterface $em;
    private CdnUploader $uploader;
    private AreaRepository $areaRepository;
    private MessageBusInterface $bus;
    private CacheInterface $cache;

    public function __construct(EntityManagerInterface $em, CdnUploader $u, AreaRepository $ar, MessageBusInterface $bus, CacheInterface $cache)
    {
        $this->em = $em;
        $this->uploader = $u;
        $this->areaRepository = $ar;
        $this->bus = $bus;
        $this->cache = $cache;
    }

    public function prepareImport(Organization $organization, File $file): Import
    {
        /*
         * Parse the file to extract columns and first lines
         */
        $firstLines = Spreadsheet::open($file)->getFirstLines(6);
        $firstLine = $firstLines[0];
        unset($firstLines[0]);

        // Create columns
        $columns = array_fill(0, count($firstLine), null);
        foreach ($firstLine as $key => $column) {
            $columns[$key] = $column;
        }

        // Create data lines
        $dataLines = [];
        foreach (array_slice($firstLines, 0, 5) as $row) {
            $dataLine = array_fill(0, count($columns), '');
            foreach ($row as $key => $value) {
                $dataLine[$key] = $value;
            }

            $dataLines[] = $dataLine;
        }

        $head = new ImportHead($columns, $dataLines, $this->guessColumnsTypes($columns, $dataLines));

        /*
         * Upload on remote FS
         */
        $upload = $this->uploader->upload(CdnUploadRequest::createOrganizationPrivateFileRequest($file));

        $import = new Import($organization, $upload, $head);

        $this->em->persist($import);
        $this->em->flush();

        return $import;
    }

    public function startImport(Import $import, ImportMetadataData $metadata)
    {
        $area = null;
        if ($metadata->areaId) {
            $area = $this->cache->get('area-'.$metadata->areaId, function (ItemInterface $item) use ($metadata) {
                $item->expiresAfter(3600 * 24 * 30 * 6); // 6 month

                return $this->areaRepository->find($metadata->areaId);
            });

            $area = $this->em->getReference($area::class, $area->getId());
        }

        $import->setArea($area);
        $import->setMatchedColumns($metadata->columnsTypes);

        $this->em->persist($import);
        $this->em->flush();

        $this->bus->dispatch(new ImportMessage($import->getId()));
    }

    private function guessColumnsTypes(array $columns, array $dataLines): array
    {
        $matched = array_fill(0, count($columns), null);

        foreach ($dataLines as $line) {
            foreach ($line as $key => $value) {
                if (!($matched[$key] ?? null)) {
                    $matched[$key] = $this->guessColumnType(trim($columns[$key] ?? ''), trim($value));
                }
            }
        }

        return $matched;
    }

    private function guessColumnType(?string $columnLabel, ?string $value): ?string
    {
        // Email value
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        return match (strtolower($columnLabel)) {
            'email', 'email address' => 'email',
            'first name', 'firstname', 'prénom', 'prenom' => 'profileFirstName',
            'last name', 'lastname', 'nom', 'nom de famille' => 'profileLastName',
            'gender', 'sex', 'sexe' => 'profileGender',
            'phone', 'phone number', 'téléphone', 'telephone' => 'contactPhone',
            'address', 'adresse' => 'addressStreetLine1',
            'zip code', 'zipcode', 'code postal' => 'addressZipCode',
            'city', 'ville' => 'addressCity',
            'country', 'pays' => 'addressCountry',
            'newsletter', 'optin', 'opt-in', 'optin time', 'optin_time' => 'settingsReceiveNewsletters',
            default => null,
        };
    }
}

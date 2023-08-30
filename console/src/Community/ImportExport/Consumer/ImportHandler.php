<?php

namespace App\Community\ImportExport\Consumer;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Api\Model\ContactApiData;
use App\Community\ContactLocator;
use App\Entity\Community\Contact;
use App\Entity\Community\Import;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\ImportRepository;
use App\Repository\Community\TagRepository;
use App\Repository\Platform\JobRepository;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Util\Spreadsheet;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

use function Symfony\Component\String\u;

/**
 * Process import files.
 */
final class ImportHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private ContactRepository $contactRepository,
        private TagRepository $tagRepository,
        private ImportRepository $importRepository,
        private JobRepository $jobRepository,
        private FilesystemReader $cdnStorage,
        private ContactLocator $contactLocator,
        private MessageBusInterface $bus,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(ImportMessage $message)
    {
        if (!$import = $this->importRepository->find($message->getImportId())) {
            $this->logger->error('Import not found by its ID', ['id' => $message->getImportId()]);

            return true;
        }

        if ($import->getJob()->isFinished()) {
            $this->logger->error('Import finished', ['id' => $message->getImportId()]);

            return true;
        }

        $orga = $import->getOrganization();
        $orgaUuid = $orga->getUuid()->toRfc4122();
        $orgaCrmIndexVersion = $orga->getCrmIndexVersion();
        $jobId = $import->getJob()->getId();

        // Parsing file
        $this->logger->info('Parsing file', ['id' => $message->getImportId()]);
        $localFile = $this->downloadImportFile($import);

        // Importing lines
        $this->logger->info('Importing lines', ['id' => $message->getImportId()]);

        try {
            $batchSize = 100;
            $steps = 0;
            $isFirstLine = true;
            $crmBatch = [];

            foreach (Spreadsheet::open($localFile) as $row) {
                ++$steps;

                if ($isFirstLine) {
                    $isFirstLine = false;
                    continue;
                }

                $row = array_values($row);

                // Create update payload
                $data = $this->createApiDataPayload($import, $row);

                $this->logger->info('Importing line', ['email' => $data->email]);

                // Fetching or creating the contact
                if (!$data->email || !$contact = $this->contactRepository->findOneByAnyEmail($orga, $data->email)) {
                    $contact = new Contact($orga, $data->email);
                }

                // Find contact country
                if ($data->addressCountry) {
                    if ($country = $this->contactLocator->findContactCountry($data->addressCountry)) {
                        $data->addressCountry = $country->getCode();
                        $contact->setCountry($country);
                    } else {
                        $data->addressCountry = null;
                    }
                }

                // Apply update
                $contact->applyApiUpdate($data, 'import');

                // Locate the contact if possible
                $contact->setArea($this->contactLocator->findContactArea($contact) ?: $import->getArea());

                // Persist a first time before synchronizing tags
                $this->em->persist($contact);
                $this->em->flush($contact);

                // Resolve new tags list and replace current tags with new ones
                if ($data->metadataTags) {
                    $resolvedNewTags = array_unique(array_merge($contact->getMetadataTagsNames(), $data->metadataTags));
                    $this->tagRepository->replaceContactTags($contact, $resolvedNewTags);
                }

                // Trigger CRM update and refresh status
                $crmBatch[$contact->getUuid()->toRfc4122()] = $contact->getId();

                if (0 === $steps % $batchSize) {
                    $this->jobRepository->setJobStep($jobId, $steps);
                    $this->bus->dispatch(new UpdateCrmDocumentsMessage($orgaUuid, $orgaCrmIndexVersion, $crmBatch));
                    $crmBatch = [];
                }
            }
        } finally {
            @unlink($localFile);
        }

        // Trigger CRM update
        $this->bus->dispatch(new UpdateCrmDocumentsMessage($orgaUuid, $orgaCrmIndexVersion, $crmBatch));

        // Trigger stats count
        $this->bus->dispatch(new RefreshContactStatsMessage($orga->getId()));

        // Mark job finished
        $this->jobRepository->setJobStep($jobId, $steps);
        $this->jobRepository->setJobTotalSteps($jobId, $steps);

        return true;
    }

    private function downloadImportFile(Import $import): File
    {
        $tempFile = sys_get_temp_dir().'/citipo-import-'.$import->getId().'.'.$import->getFile()->getExtension();
        file_put_contents($tempFile, $this->cdnStorage->readStream($import->getFile()->getPathname()));

        return new File($tempFile);
    }

    private function createApiDataPayload(Import $import, array $line): ContactApiData
    {
        $data = new ContactApiData();
        $data->metadataSource = 'import:'.$import->getId();
        $data->metadataTags = [];

        $columnsTypes = $import->getHead()->getMatchedColumns();

        foreach ($line as $column => $value) {
            $this->mapValue($data, $columnsTypes[$column] ?? 'ignored', trim($value));
        }

        return $data;
    }

    private function mapValue(ContactApiData $data, string $type, $value)
    {
        switch ($type) {
            case 'ignored':
                return;

            case 'email':
                $data->email = u($value)->slice(0, 250)->toString();
                break;

            case 'profileFormalTitle':
                $data->profileFormalTitle = u($value)->slice(0, 20)->toString();
                break;

            case 'profileFirstName':
                $data->profileFirstName = u($value)->slice(0, 150)->toString();
                break;

            case 'profileMiddleName':
                $data->profileMiddleName = u($value)->slice(0, 150)->toString();
                break;

            case 'profileLastName':
                $data->profileLastName = u($value)->slice(0, 150)->toString();
                break;

            case 'profileBirthdate':
                try {
                    $data->profileBirthdate = (new \DateTime($value))->format('Y-m-d');
                } catch (\Exception $e) {
                }
                break;

            case 'profileGender':
                // Try to guess the gender based on the first letter of the word
                $map = ['h' => 'male', 'm' => 'male', 'f' => 'female'];
                $data->profileGender = $map[u($value)->slice(0, 1)->lower()->toString()] ?? null;
                break;

            case 'profileCompany':
                $data->profileCompany = u($value)->slice(0, 150)->toString();
                break;

            case 'profileJobTitle':
                $data->profileJobTitle = u($value)->slice(0, 150)->toString();
                break;

            case 'contactPhone':
                $data->contactPhone = u($value)->slice(0, 50)->toString();
                break;

            case 'contactWorkPhone':
                $data->contactWorkPhone = u($value)->slice(0, 50)->toString();
                break;

            case 'socialFacebook':
                $data->socialFacebook = u($value)->slice(0, 150)->toString();
                break;

            case 'socialTwitter':
                $data->socialTwitter = u($value)->slice(0, 150)->toString();
                break;

            case 'socialLinkedIn':
                $data->socialLinkedIn = u($value)->slice(0, 150)->toString();
                break;

            case 'socialTelegram':
                $data->socialTelegram = u($value)->slice(0, 150)->toString();
                break;

            case 'socialWhatsapp':
                $data->socialWhatsapp = u($value)->slice(0, 150)->toString();
                break;

            case 'addressStreetLine1':
                $data->addressStreetLine1 = u($value)->slice(0, 150)->toString();
                break;

            case 'addressStreetLine2':
                $data->addressStreetLine2 = u($value)->slice(0, 150)->toString();
                break;

            case 'addressZipCode':
                $data->addressZipCode = u($value)->slice(0, 150)->toString();
                break;

            case 'addressCity':
                $data->addressCity = u($value)->slice(0, 150)->toString();
                break;

            case 'addressCountry':
                $data->addressCountry = u($value)->slice(0, 50)->toString();
                break;

            case 'settingsReceiveNewsletters':
                $data->settingsReceiveNewsletters = $this->normalizeBoolean($value);
                break;

            case 'settingsReceiveSms':
                $data->settingsReceiveSms = $this->normalizeBoolean($value);
                break;

            case 'settingsReceiveCalls':
                $data->settingsReceiveCalls = $this->normalizeBoolean($value);
                break;

            case 'metadataComment':
                $data->metadataComment = $value;
                break;

            case 'metadataTagsList':
                foreach (array_map('trim', explode(',', $value)) as $tag) {
                    $data->metadataTags[] = $tag;
                }

                break;

            case 'metadataTag':
                $data->metadataTags[] = $value;
                break;

            default:
                throw new \InvalidArgumentException('Invalid type '.$type);
        }
    }

    private function normalizeBoolean(string $value): bool
    {
        $value = strtolower(trim($value));

        return !(
            !$value
            || '0' === $value
            || 'false' === $value
            || 'f' === $value
            || 'null' === $value
            || 'n' === $value
            || 'non' === $value
            || 'no' === $value
        );
    }
}

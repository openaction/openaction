<?php

namespace App\Entity\Website;

use App\Entity\Util;
use App\Repository\Website\FormBlockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormBlockRepository::class)]
#[ORM\Table('website_forms_blocks')]
class FormBlock
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    public const TYPE_NEWSLETTER = 'newsletter';

    // Normal fields
    public const TYPE_TEXT = 'text';
    public const TYPE_TEXTAREA = 'textarea';
    public const TYPE_SELECT = 'select';
    public const TYPE_RADIO = 'radio';
    public const TYPE_RATING = 'rating';
    public const TYPE_MONEY_AMOUNT = 'money_amount';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_CONFIRMATION = 'confirmation';
    public const TYPE_DATE = 'date';
    public const TYPE_TIME = 'time';
    public const TYPE_FILE = 'file';

    // Automatic fields
    public const TYPE_EMAIL = 'email';
    public const TYPE_FORMAL_TITLE = 'formal_title';
    public const TYPE_FIRST_NAME = 'firstname';
    public const TYPE_MIDDLE_NAME = 'middlename';
    public const TYPE_LAST_NAME = 'lastname';
    public const TYPE_BIRTHDATE = 'birthdate';
    public const TYPE_GENDER = 'gender';
    public const TYPE_NATIONALITY = 'nationality';
    public const TYPE_COMPANY = 'company';
    public const TYPE_JOB_TITLE = 'job_title';
    public const TYPE_PHONE = 'phone';
    public const TYPE_WORK_PHONE = 'work_phone';
    public const TYPE_SOCIAL_FACEBOOK = 'social_facebook';
    public const TYPE_SOCIAL_TWITTER = 'social_twitter';
    public const TYPE_SOCIAL_LINKEDIN = 'social_linkedin';
    public const TYPE_SOCIAL_TELEGRAM = 'social_telegram';
    public const TYPE_SOCIAL_WHATSAPP = 'social_whatsapp';
    public const TYPE_STREET_ADDRESS = 'street_address';
    public const TYPE_STREET_ADDRESS_2 = 'street_address_2';
    public const TYPE_CITY = 'city';
    public const TYPE_ZIP_CODE = 'zip_code';
    public const TYPE_COUNTRY = 'country';
    public const TYPE_PICTURE = 'picture';

    // Tags fields
    public const TYPE_TAG_RADIO = 'tag_radio';
    public const TYPE_TAG_CHECKBOX = 'tag_checkbox';
    public const TYPE_TAG_HIDDEN = 'tag_hidden';

    // Custom content
    public const TYPE_HEADER = 'header';
    public const TYPE_PARAGRAPH = 'paragraph';
    public const TYPE_HTML = 'html';

    // List of blocks that are not fields, meaning they are not expecting any value
    private const STATIC_BLOCKS = [
        self::TYPE_HEADER,
        self::TYPE_PARAGRAPH,
        self::TYPE_HTML,
    ];

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'blocks')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Form $form;

    #[ORM\Column(length: 30)]
    private string $type;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'boolean')]
    private bool $required;

    #[ORM\Column(type: 'integer')]
    private int $weight = 0;

    #[ORM\Column(type: 'json')]
    private array $config;

    public function __construct(Form $form, string $type, string $content, bool $required = false)
    {
        $this->populateTimestampable();
        $this->form = $form;
        $this->type = $type;
        $this->content = $content;
        $this->required = $required;
        $this->config = [];
    }

    public static function createFromData(Form $form, array $data, int $weight = 1)
    {
        $self = new self($form, (string) $data['type'], (string) $data['content'], (bool) $data['required']);
        $self->config = (array) ($data['config'] ?? []);
        $self->weight = $weight;

        return $self;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['form'], $data['type'], $data['content'], $data['required'] ?? false);
        $self->weight = $data['weight'] ?? 1;
        $self->config = $data['config'] ?? [];

        return $self;
    }

    public function duplicate(Form $form): self
    {
        $self = new self($form, $this->type, $this->content, $this->required);
        $self->weight = $this->weight;
        $self->config = $this->config;

        return $self;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isField(): bool
    {
        return !in_array($this->type, self::STATIC_BLOCKS, true);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}

<?php

namespace App\FormBuilder;

use App\Bridge\Uploadcare\Uploadcare;
use App\Client\Model\ApiResource;
use App\Form\CountryType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class SymfonyFormBuilder
{
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

    public function __construct(
        private FormFactoryInterface $formFactory,
        private TranslatorInterface $translator,
        private Uploadcare $uploadcare,
    ) {
    }

    public function createFromBlocks(array $blocksData, array $data = [], bool $addPrivacyField = true): FormInterface
    {
        $values = [];
        foreach ($blocksData as $key => $block) {
            if ($block->field) {
                $values['field'.$key] = !empty($data['field'.$key]) ? $data['field'.$key] : $this->getFieldDefaultValue($block);
            }
        }

        $builder = $this->formFactory->createBuilder(FormType::class, $values, ['csrf_protection' => false]);

        foreach ($blocksData as $key => $block) {
            if ($block->field) {
                $builder->add('field'.$key, $this->getFieldType($block), $this->createFieldOptions($block));
            }
        }

        if ($addPrivacyField) {
            $builder->add('privacy', CheckboxType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
                'mapped' => false,
            ]);
        }

        return $builder->getForm();
    }

    public function normalizeFormData(array $blocksData, array $formData): array
    {
        $mapped = [];
        foreach ($blocksData as $key => $block) {
            if (!$block->field) {
                continue;
            }

            $value = $formData['field'.$key];
            if (self::TYPE_DATE === $block->type || self::TYPE_BIRTHDATE === $block->type) {
                $value = $value?->format('Y-m-d');
            } elseif (self::TYPE_TIME === $block->type) {
                $value = $value?->format('H:i');
            } elseif (is_bool($value)) {
                $value = $value ? '1' : '0';
            } elseif (is_array($value)) {
                $value = implode(', ', $value);
            } elseif ($value instanceof UploadedFile) {
                $value = $value->getClientOriginalName();
            } else {
                $value = (string) $value;
            }

            $mapped[] = $value;
        }

        return $mapped;
    }

    public function getEmailValue(array $blocksData, array $formData): ?string
    {
        foreach ($blocksData as $key => $block) {
            if ($block->field && self::TYPE_EMAIL === $block->type && ($email = trim($formData['field'.$key]))) {
                return $email;
            }
        }

        return null;
    }

    public function getPictureValue(array $blocksData, array $formData): ?UploadedFile
    {
        foreach ($blocksData as $key => $block) {
            if ($block->field && self::TYPE_PICTURE === $block->type && ($picture = $formData['field'.$key])) {
                return $picture;
            }
        }

        return null;
    }

    private function getFieldDefaultValue(ApiResource $block)
    {
        if ('newsletter' === $block->type) {
            return false;
        }

        return null;
    }

    private function getFieldType(ApiResource $block): string
    {
        switch ($block->type) {
            case self::TYPE_TEXTAREA:
                return TextareaType::class;

            case self::TYPE_EMAIL:
                return EmailType::class;

            case self::TYPE_NATIONALITY:
            case self::TYPE_COUNTRY:
                return CountryType::class;

            case self::TYPE_DATE:
                return DateType::class;

            case self::TYPE_BIRTHDATE:
                return BirthdayType::class;

            case self::TYPE_TIME:
                return TimeType::class;

            case self::TYPE_PICTURE:
                return FileType::class;

            case self::TYPE_CHECKBOX:
            case self::TYPE_CONFIRMATION:
            case self::TYPE_RADIO:
            case self::TYPE_RATING:
            case self::TYPE_SELECT:
            case self::TYPE_FORMAL_TITLE:
            case self::TYPE_GENDER:
            case self::TYPE_TAG_RADIO:
            case self::TYPE_TAG_CHECKBOX:
                return ChoiceType::class;

            case self::TYPE_MONEY_AMOUNT:
                return NumberType::class;

            case self::TYPE_TAG_HIDDEN:
                return HiddenType::class;

            case self::TYPE_NEWSLETTER:
                return CheckboxType::class;
        }

        return TextType::class;
    }

    private function createFieldOptions(ApiResource $block): array
    {
        $options = [
            'label' => $block->content,
            'required' => $block->required,
            'constraints' => array_merge($block->required ? [new NotBlank()] : [], $this->createFieldConstraints($block)),
        ];

        $choiceFields = [
            self::TYPE_CHECKBOX,
            self::TYPE_RADIO,
            self::TYPE_RATING,
            self::TYPE_SELECT,
            self::TYPE_TAG_RADIO,
            self::TYPE_TAG_CHECKBOX,
        ];

        if (in_array($block->type, $choiceFields, true)) {
            $options = array_merge($options, [
                'placeholder' => 'forms.no_value',
                'multiple' => in_array($block->type, ['checkbox', 'tag_checkbox'], true),
                'expanded' => 'select' !== $block->type,
                'choices' => 'formal_title' === $block->type ? ['M'] : $block->config['choices'],
                'choice_label' => static fn ($choice) => $choice,
            ]);
        }

        if (self::TYPE_CONFIRMATION === $block->type) {
            $options = array_merge($options, [
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => ['1'],
                'choice_label' => static fn () => $block->content,
            ]);
        }

        if (self::TYPE_DATE === $block->type) {
            $options = array_merge($options, [
                'years' => range(((int) date('Y')) - 120, ((int) date('Y')) + 5),
            ]);
        }

        if (self::TYPE_FORMAL_TITLE === $block->type) {
            $options = array_merge($options, [
                'placeholder' => 'forms.no_value',
                'expanded' => true,
                'choice_label' => static fn ($choice) => $choice,
                'choices' => [
                    $this->translator->trans('base.formal_title.madam'),
                    $this->translator->trans('base.formal_title.sir'),
                ],
            ]);
        }

        if (self::TYPE_GENDER === $block->type) {
            $options = array_merge($options, [
                'placeholder' => 'forms.no_value',
                'expanded' => true,
                'choices' => [
                    $this->translator->trans('base.gender.female') => 'female',
                    $this->translator->trans('base.gender.male') => 'male',
                    $this->translator->trans('base.gender.transgender') => 'transgender',
                    $this->translator->trans('base.gender.non_binary') => 'non_binary',
                    $this->translator->trans('base.gender.other') => 'other',
                ],
            ]);
        }

        if (self::TYPE_TAG_HIDDEN === $block->type) {
            $options['data'] = $block->config['tags'];
        }

        if (self::TYPE_FILE === $block->type) {
            $uploadKey = $this->uploadcare->generateUploadKey();

            $options['attr'] = [
                'class' => 'uploadcare-field',
                'data-controller' => 'uploadcare-field',
                'data-uploadcare-field-public-key-value' => $this->uploadcare->getPublicKey(),
                'data-uploadcare-field-signature-value' => $uploadKey->getSignature(),
                'data-uploadcare-field-expire-value' => (string) $uploadKey->getExpire(),
            ];
        }

        if (self::TYPE_MONEY_AMOUNT === $block->type) {
            $options = array_merge($options, [
                'attr' => [
                    'data-controller' => 'money-amount-field',
                    'data-money-amount-field-suggestions-value' => implode('|', $block->config['choices']),
                    'data-money-amount-field-custom-label-value' => $this->translator->trans('forms.money_amount_custom'),
                ],
            ]);
        }

        return $options;
    }

    private function createFieldConstraints(ApiResource $block): array
    {
        return match ($block->type) {
            self::TYPE_EMAIL => [
                new Email(),
                new Length(['max' => 250]),
            ],

            self::TYPE_COUNTRY, self::TYPE_NATIONALITY => [
                new Country(),
                new Length(['max' => 2]),
            ],

            self::TYPE_FORMAL_TITLE => [
                new Length(['max' => 20]),
            ],

            self::TYPE_PHONE, self::TYPE_WORK_PHONE => [
                new Length(['max' => 50]),
            ],

            self::TYPE_FIRST_NAME,
            self::TYPE_MIDDLE_NAME,
            self::TYPE_LAST_NAME,
            self::TYPE_COMPANY,
            self::TYPE_JOB_TITLE,
            self::TYPE_STREET_ADDRESS,
            self::TYPE_STREET_ADDRESS_2,
            self::TYPE_CITY,
            self::TYPE_ZIP_CODE,
            self::TYPE_SOCIAL_FACEBOOK,
            self::TYPE_SOCIAL_TWITTER,
            self::TYPE_SOCIAL_LINKEDIN,
            self::TYPE_SOCIAL_TELEGRAM,
            self::TYPE_SOCIAL_WHATSAPP => [
                new Length(['max' => 150]),
            ],

            self::TYPE_MONEY_AMOUNT => [
                new GreaterThan(0),
            ],

            self::TYPE_PICTURE => [
                new Image([
                    'maxSize' => '5Mi',
                    'mimeTypes' => [
                        'image/bmp',
                        'image/x-ms-bmp',
                        'image/jpeg',
                        'image/pjpeg',
                        'image/png',
                        'image/webp',
                    ],
                ]),
            ],

            default => [],
        };
    }
}

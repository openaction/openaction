<?php

namespace App\Entity\Community\Model;

use App\Entity\Community\ContentImport;

/**
 * Contains constants and properties needed for settings for content import.
 */
class ContentImportSettings
{
    /**
     * General.
     */
    public const ALLOWED_IMAGE_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
    ];

    /**
     * WordPress import related constants and attributes.
     */
    public const IMPORT_SOURCE_WORDPRESS = 'wordpress';

    public const POST_STATUS_SAVE_AS_DRAFT = 'save_as_draft';
    public const POST_STATUS_SAVE_AS_ORIGINAL = 'save_as_original';
    public const POST_STATUS_PUBLISH = 'publish';
    public const POST_STATUS_DRAFT = 'draft';

    public const KEEP_CATEGORIES_YES = 'keep_categories_yes';
    public const KEEP_CATEGORIES_NO = 'keep_categories_no';

    public string $postSaveStatus;
    public string $keepCategories;
    public ?string $postAuthorsIds = null;

    public static function createFromImport(ContentImport $import): self
    {
        if (self::IMPORT_SOURCE_WORDPRESS === $import->getSource()) {
            return new self();
        }

        throw new \RuntimeException('Missing import source in content import settings');
    }
}

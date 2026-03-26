<?php

namespace App\Community\MergeTag;

final class ContactMergeTags
{
    /**
     * Legacy merge tags kept for backward compatibility in existing templates.
     *
     * @var array<string, string>
     */
    public const LEGACY_TO_CANONICAL = [
        '-contact-formaltitle-' => '-contact-formal-title-',
        '-contact-jobtitle-' => '-contact-job-title-',
        '-contact-streetline1-' => '-contact-streetline-1-',
        '-contact-streetline2-' => '-contact-streetline-2-',
    ];

    /**
     * @var array<string, string>
     */
    private const CANONICAL_TO_LEGACY = [
        '-contact-formal-title-' => '-contact-formaltitle-',
        '-contact-job-title-' => '-contact-jobtitle-',
        '-contact-streetline-1-' => '-contact-streetline1-',
        '-contact-streetline-2-' => '-contact-streetline2-',
    ];

    /**
     * @param array<string, mixed> $variables
     *
     * @return array<string, mixed>
     */
    public static function withLegacyAliases(array $variables): array
    {
        foreach (self::CANONICAL_TO_LEGACY as $canonical => $legacy) {
            if (\array_key_exists($canonical, $variables) && !\array_key_exists($legacy, $variables)) {
                $variables[$legacy] = $variables[$canonical];
            }
        }

        return $variables;
    }
}

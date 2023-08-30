<?php

namespace App\Theme\Consumer;

final class SyncThemeMessage
{
    private int $themeId;

    public function __construct(int $themeId)
    {
        $this->themeId = $themeId;
    }

    public function getThemeId(): int
    {
        return $this->themeId;
    }
}

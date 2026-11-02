<?php

declare(strict_types=1);

namespace App\Application\Shared;

use App\Domain\PageContent\Value\ImageFormat;

interface ImageStorageInterface
{
    /**
     * Stores the image under a generated name and returns its public path, e.g. `/uploads/<name>.jpg`.
     */
    public function store(string $bytes, ImageFormat $format): string;
}

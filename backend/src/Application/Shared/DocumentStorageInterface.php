<?php

declare(strict_types=1);

namespace App\Application\Shared;

use App\Domain\PageContent\Value\DocumentFormat;

interface DocumentStorageInterface
{
    /**
     * Stores the Document under a fresh unique folder and the given name; returns its public path,
     * e.g. `/uploads/files/<uuid>/<name>.pdf`.
     */
    public function store(string $bytes, string $name, DocumentFormat $format): string;
}

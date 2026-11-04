<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\DocumentStorageInterface;
use App\Domain\PageContent\Value\DocumentFormat;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * Writes Documents into `files/<uuid>/` of the uploads directory, which nginx serves at `/uploads/files/`
 * as downloads. The unique folder keeps the readable name and still never overwrites a file.
 */
final readonly class LocalDocumentStorage implements DocumentStorageInterface
{
    public function __construct(
        private string $directory,
    ) {}

    public function store(string $bytes, string $name, DocumentFormat $format): string
    {
        $folder = 'files/' . Uuid::uuid7()->toString();
        $path = $this->directory . '/' . $folder;
        if (!mkdir($path, 0755, true) && !is_dir($path)) {
            throw new RuntimeException('Cannot create the document directory.');
        }

        $file = $name . '.' . $format->extension();
        if (file_put_contents($path . '/' . $file, $bytes) === false) {
            throw new RuntimeException('Cannot write the uploaded document.');
        }

        return '/uploads/' . $folder . '/' . $file;
    }
}

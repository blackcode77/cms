<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\ImageStorageInterface;
use App\Domain\PageContent\Value\ImageFormat;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * Writes uploads into a local directory that nginx serves at `/uploads/`.
 */
final readonly class LocalImageStorage implements ImageStorageInterface
{
    public function __construct(
        private string $directory,
    ) {}

    public function store(string $bytes, ImageFormat $format): string
    {
        if (!is_dir($this->directory) && !mkdir($this->directory, 0755, true) && !is_dir($this->directory)) {
            throw new RuntimeException('Cannot create the uploads directory.');
        }

        $name = Uuid::uuid7()->toString() . '.' . $format->extension();
        if (file_put_contents($this->directory . '/' . $name, $bytes) === false) {
            throw new RuntimeException('Cannot write the uploaded image.');
        }

        return '/uploads/' . $name;
    }
}

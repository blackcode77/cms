<?php

declare(strict_types=1);

namespace App\Application\PageContent\UploadImage;

use App\Application\PageContent\Exception\InvalidImageUploadException;
use App\Application\Shared\ImageStorageInterface;
use App\Domain\PageContent\Value\ImageFormat;

final readonly class Handler
{
    public const int MAX_BYTES = 5 * 1024 * 1024;

    public function __construct(
        private ImageStorageInterface $storage,
    ) {}

    /**
     * @return string Public path of the stored image.
     * @throws InvalidImageUploadException
     */
    public function handle(string $bytes): string
    {
        if (strlen($bytes) > self::MAX_BYTES) {
            throw InvalidImageUploadException::tooLarge(self::MAX_BYTES);
        }

        $format = ImageFormat::detect($bytes) ?? throw InvalidImageUploadException::unsupportedFormat();

        return $this->storage->store($bytes, $format);
    }
}

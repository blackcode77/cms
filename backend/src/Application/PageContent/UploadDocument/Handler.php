<?php

declare(strict_types=1);

namespace App\Application\PageContent\UploadDocument;

use App\Application\PageContent\Exception\InvalidDocumentUploadException;
use App\Application\Shared\DocumentStorageInterface;
use App\Domain\PageContent\Value\DocumentFormat;
use App\Domain\PageContent\Value\DocumentName;

final readonly class Handler
{
    public const int MAX_BYTES = 20 * 1024 * 1024;

    public function __construct(
        private DocumentStorageInterface $storage,
    ) {}

    /**
     * @return string Public path of the stored Document.
     * @throws InvalidDocumentUploadException
     */
    public function handle(string $bytes, ?string $clientName): string
    {
        if (strlen($bytes) > self::MAX_BYTES) {
            throw InvalidDocumentUploadException::tooLarge(self::MAX_BYTES);
        }

        $format = DocumentFormat::detect($bytes) ?? throw InvalidDocumentUploadException::unsupportedFormat();

        return $this->storage->store($bytes, DocumentName::fromClientName($clientName), $format);
    }
}

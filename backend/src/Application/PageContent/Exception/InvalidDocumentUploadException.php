<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidDocumentUploadException extends Exception
{
    public static function tooLarge(int $limitBytes): self
    {
        return new self(sprintf('File must not exceed %d MB.', intdiv($limitBytes, 1024 * 1024)));
    }

    public static function unsupportedFormat(): self
    {
        return new self('Only PDF, Excel (XLSX, XLS) and Word (DOCX, DOC) files are accepted.');
    }
}

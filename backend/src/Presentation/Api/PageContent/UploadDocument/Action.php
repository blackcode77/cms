<?php

declare(strict_types=1);

namespace App\Presentation\Api\PageContent\UploadDocument;

use App\Application\PageContent\UploadDocument\Handler;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * `POST /uploads/file` (multipart, field `file`), requires `edit_page`. Returns `{url, size}` (size in bytes).
 */
final readonly class Action
{
    private const string TOO_LARGE = 'File must not exceed 20 MB.';

    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $file = $request->getUploadedFiles()['file'] ?? null;
        if (!$file instanceof UploadedFileInterface) {
            return $this->responseFactory->fail('Send the document in the "file" field.');
        }
        if ($file->getError() === UPLOAD_ERR_INI_SIZE || $file->getError() === UPLOAD_ERR_FORM_SIZE) {
            return $this->responseFactory->fail(self::TOO_LARGE);
        }
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return $this->responseFactory->fail('The document was not uploaded.');
        }
        if (($file->getSize() ?? 0) > Handler::MAX_BYTES) {
            return $this->responseFactory->fail(self::TOO_LARGE);
        }

        $bytes = (string) $file->getStream();
        $url = $this->handler->handle($bytes, $file->getClientFilename());

        return $this->responseFactory->success(['url' => $url, 'size' => strlen($bytes)])->withStatus(201);
    }
}

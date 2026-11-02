<?php

declare(strict_types=1);

namespace App\Presentation\Api\PageContent\UploadImage;

use App\Application\PageContent\UploadImage\Handler;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * `POST /uploads/image` (multipart, field `file`), requires `edit_page`.
 */
final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $file = $request->getUploadedFiles()['file'] ?? null;
        if (!$file instanceof UploadedFileInterface) {
            return $this->responseFactory->fail('Send the image in the "file" field.');
        }
        if ($file->getError() === UPLOAD_ERR_INI_SIZE || $file->getError() === UPLOAD_ERR_FORM_SIZE) {
            return $this->responseFactory->fail('Image must not exceed 5 MB.');
        }
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return $this->responseFactory->fail('The image was not uploaded.');
        }
        if (($file->getSize() ?? 0) > Handler::MAX_BYTES) {
            return $this->responseFactory->fail('Image must not exceed 5 MB.');
        }

        $url = $this->handler->handle((string) $file->getStream());

        return $this->responseFactory->success(['url' => $url])->withStatus(201);
    }
}

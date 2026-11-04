<?php

declare(strict_types=1);

namespace App\Presentation\Api\PageContent\Update;

use App\Application\PageContent\UpdateElement\Command;
use App\Application\PageContent\UpdateElement\Handler;
use App\Domain\PageContent\Value\ContentPath;
use App\Domain\PageContent\Value\ElementPatch;
use App\Domain\PageContent\Value\PageKey;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;
use Yiisoft\Router\CurrentRoute;

/**
 * `PATCH /page-content/{key}`, requires `edit_page`.
 */
final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
        private CurrentRoute $route,
    ) {}

    public function __invoke(Input $input): ResponseInterface
    {
        Assert::stringNotEmpty($input->path);

        $name = (string) $this->route->getArgument('key');
        Assert::stringNotEmpty($name);

        try {
            $key = new PageKey($name);
        } catch (InvalidArgumentException) {
            return $this->responseFactory->notFound('Page not found.');
        }

        $this->handler->handle(
            new Command(
                $key,
                new ContentPath($input->path),
                new ElementPatch($input->contentType(), $input->contentValue(), $input->enabled),
            ),
        );

        return $this->responseFactory->success();
    }
}

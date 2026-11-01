<?php

declare(strict_types=1);

namespace App\Presentation\Api\Page\Create;

use App\Application\Page\CreatePage\Command;
use App\Application\Page\CreatePage\Handler;
use App\Domain\Page\Value\PageSlug;
use App\Domain\Page\Value\PageTitle;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
    ) {}

    public function __invoke(Input $input): ResponseInterface
    {
        // Input rules (Required) already guarantee this; the asserts only narrow the types.
        Assert::stringNotEmpty($input->title);
        Assert::stringNotEmpty($input->slug);

        $result = $this->handler->handle(
            new Command(
                new PageTitle($input->title),
                new PageSlug($input->slug),
                $input->content,
            ),
        );

        return $this->responseFactory
            ->success(['id' => $result->id->toString()])
            ->withStatus(201);
    }
}

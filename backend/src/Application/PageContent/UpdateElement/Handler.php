<?php

declare(strict_types=1);

namespace App\Application\PageContent\UpdateElement;

use App\Application\PageContent\Exception\ContentValueTooLongException;
use App\Application\Shared\HtmlSanitizerInterface;
use App\Domain\PageContent\Repository\PageContentRepositoryInterface;
use App\Domain\PageContent\Value\ContentType;
use App\Domain\PageContent\Value\ElementPatch;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class Handler
{
    public function __construct(
        private PageContentRepositoryInterface $repository,
        private HtmlSanitizerInterface $htmlSanitizer,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws ContentValueTooLongException
     */
    public function handle(Command $command): void
    {
        $patch = $command->patch;

        if ($patch->type !== null && $patch->value !== null) {
            if (mb_strlen($patch->value) > $patch->type->maxLength()) {
                throw ContentValueTooLongException::forType($patch->type);
            }
            if ($patch->type === ContentType::Html) {
                $patch = new ElementPatch($patch->type, $this->htmlSanitizer->sanitize($patch->value), $patch->enabled);
            }
        }

        $this->repository->patchElement(
            $command->key,
            $command->path,
            $patch,
            DateTimeImmutable::createFromInterface($this->clock->now()),
        );
    }
}

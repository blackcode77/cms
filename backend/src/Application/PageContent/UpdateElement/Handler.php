<?php

declare(strict_types=1);

namespace App\Application\PageContent\UpdateElement;

use App\Application\PageContent\Exception\ContentValueTooLongException;
use App\Application\PageContent\Exception\InvalidColorException;
use App\Application\PageContent\Exception\InvalidDocumentUrlException;
use App\Application\PageContent\Exception\InvalidIconNameException;
use App\Application\PageContent\Exception\InvalidImageUrlException;
use App\Application\PageContent\Exception\InvalidLinkException;
use App\Application\PageContent\Exception\InvalidRouteStopsException;
use App\Application\PageContent\Exception\InvalidUrlException;
use App\Application\PageContent\Exception\TooManyItemsException;
use App\Application\Shared\HtmlSanitizerInterface;
use App\Domain\PageContent\Repository\PageContentRepositoryInterface;
use App\Domain\PageContent\Value\ColorHex;
use App\Domain\PageContent\Value\ContentList;
use App\Domain\PageContent\Value\ContentType;
use App\Domain\PageContent\Value\DocumentUrl;
use App\Domain\PageContent\Value\ExternalUrl;
use App\Domain\PageContent\Value\IconName;
use App\Domain\PageContent\Value\ImageUrl;
use App\Domain\PageContent\Value\LinkPath;
use App\Domain\PageContent\Value\RouteStops;
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
     * @throws InvalidImageUrlException
     * @throws InvalidIconNameException
     * @throws InvalidLinkException
     * @throws InvalidUrlException
     * @throws InvalidDocumentUrlException
     * @throws InvalidColorException
     * @throws InvalidRouteStopsException
     * @throws TooManyItemsException
     */
    public function handle(Command $command): void
    {
        $patch = $command->patch;

        if ($patch->type !== null && $patch->value instanceof ContentList) {
            $patch = $patch->withValue($this->checkList($patch->value));
        } elseif ($patch->type !== null && $patch->value !== null) {
            $patch = $patch->withValue($this->checkValue($patch->type, $patch->value));
        }

        $this->repository->patchElement(
            $command->key,
            $command->path,
            $patch,
            DateTimeImmutable::createFromInterface($this->clock->now()),
        );
    }

    /**
     * Applies the type's rules to one value and returns what is stored (sanitized for `html`).
     *
     * @throws ContentValueTooLongException
     * @throws InvalidImageUrlException
     * @throws InvalidIconNameException
     * @throws InvalidLinkException
     * @throws InvalidUrlException
     * @throws InvalidDocumentUrlException
     * @throws InvalidColorException
     * @throws InvalidRouteStopsException
     */
    private function checkValue(ContentType $type, string $value): string
    {
        if (mb_strlen($value) > $type->maxLength()) {
            throw ContentValueTooLongException::forType($type);
        }

        return match ($type) {
            ContentType::Image => ImageUrl::isValid($value) ? $value : throw InvalidImageUrlException::create(),
            ContentType::Icon => IconName::isValid($value) ? $value : throw InvalidIconNameException::create(),
            ContentType::Link => LinkPath::isValid($value) ? $value : throw InvalidLinkException::create(),
            ContentType::Url => ExternalUrl::isValid($value) ? $value : throw InvalidUrlException::create(),
            ContentType::File => DocumentUrl::isValid($value) ? $value : throw InvalidDocumentUrlException::create(),
            ContentType::Html => $this->htmlSanitizer->sanitize($value),
            ContentType::Color => ColorHex::normalize($value) ?? throw InvalidColorException::create(),
            ContentType::Stops => RouteStops::normalize($value) ?? throw InvalidRouteStopsException::create(),
            default => $value,
        };
    }

    /**
     * @throws ContentValueTooLongException
     * @throws InvalidImageUrlException
     * @throws InvalidIconNameException
     * @throws InvalidLinkException
     * @throws InvalidUrlException
     * @throws InvalidDocumentUrlException
     * @throws InvalidColorException
     * @throws InvalidRouteStopsException
     * @throws TooManyItemsException
     */
    private function checkList(ContentList $list): ContentList
    {
        if (count($list->items) > ContentList::MAX_ITEMS) {
            throw TooManyItemsException::create();
        }

        $list = $list->mapValues(fn(ContentType $type, string $value): string => $this->checkValue($type, $value));

        $encoded = json_encode($list->items, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        if (mb_strlen($encoded) > ContentType::List->maxLength()) {
            throw ContentValueTooLongException::forType(ContentType::List);
        }

        return $list;
    }
}

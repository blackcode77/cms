<?php

declare(strict_types=1);

namespace App\Presentation\Api\PageContent\Read;

use App\Domain\PageContent\Value\PageKey;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\PageContent\PageContentReader;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Public. `GET /page-content?keys=site,home`.
 */
final readonly class Action
{
    private const int MAX_KEYS = 10;

    public function __construct(
        private ResponseFactory $responseFactory,
        private PageContentReader $reader,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        if ($query === []) {
            parse_str($request->getUri()->getQuery(), $query);
        }
        $raw = $query['keys'] ?? '';
        $names = [];
        foreach (is_string($raw) ? explode(',', $raw) : [] as $name) {
            if ($name !== '' && !in_array($name, $names, true)) {
                $names[] = $name;
            }
        }

        if ($names === [] || count($names) > self::MAX_KEYS) {
            return $this->responseFactory->fail('Pass between 1 and ' . self::MAX_KEYS . ' page keys in "keys".');
        }

        try {
            $keys = array_map(static fn(string $name): PageKey => new PageKey($name), $names);
        } catch (InvalidArgumentException) {
            return $this->responseFactory->fail('Invalid page key.');
        }

        // Objects, so an empty page or element map is `{}` and not `[]` in JSON.
        $pages = array_map(
            static fn(array $elements): object => (object) $elements,
            $this->reader->find($keys),
        );

        return $this->responseFactory->success((object) $pages);
    }
}

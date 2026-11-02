<?php

declare(strict_types=1);

namespace App\Shared\Read\PageContent;

use App\Domain\PageContent\Value\PageKey;
use App\Infrastructure\Table;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class PageContentReader
{
    public function __construct(
        private ConnectionInterface $db,
    ) {}

    /**
     * Saved element values per requested page key; a page with no edits maps to an empty array.
     *
     * @param list<PageKey> $keys
     * @return array<string, array<string, array{type?: string, value?: string, enabled?: bool}>>
     */
    public function find(array $keys): array
    {
        $names = array_map(static fn(PageKey $key): string => $key->toString(), $keys);
        $result = array_fill_keys($names, []);
        if ($names === []) {
            return $result;
        }

        /** @var list<array{page_key: string, content: string}> $rows */
        $rows = $this->db->createQuery()
            ->select(['page_key', 'content'])
            ->from(Table::PAGE_CONTENT)
            ->where(['page_key' => $names])
            ->all();

        foreach ($rows as $row) {
            /** @var array<string, array{type?: string, value?: string, enabled?: bool}> $content */
            $content = json_decode($row['content'], true, flags: JSON_THROW_ON_ERROR);
            $result[$row['page_key']] = $content;
        }

        return $result;
    }
}

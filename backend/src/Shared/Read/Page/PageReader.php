<?php

declare(strict_types=1);

namespace App\Shared\Read\Page;

use App\Domain\Page\Value\PageId;
use App\Domain\Page\Value\PageSlug;
use App\Domain\Page\Value\PageTitle;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class PageReader
{
    public function __construct(
        private ConnectionInterface $db,
    ) {}

    /**
     * @return list<PageItem>
     */
    public function find(): array
    {
        /** @var list<PageItem> */
        return $this->db->createQuery()
            ->select(['id', 'title', 'slug', 'created_at'])
            ->from(Table::PAGE)
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->resultCallback(
                static function (array $rows): array {
                    /**
                     * @var non-empty-list<array{
                     *     id: string,
                     *     title: non-empty-string,
                     *     slug: non-empty-string,
                     *     created_at: string,
                     * }> $rows
                     */
                    return array_map(
                        static fn(array $row) => new PageItem(
                            PageId::fromString($row['id']),
                            new PageTitle($row['title']),
                            new PageSlug($row['slug']),
                            new DateTimeImmutable($row['created_at']),
                        ),
                        $rows,
                    );
                },
            )
            ->all();
    }
}

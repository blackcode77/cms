<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Page\Page;
use App\Domain\Page\PageId;
use App\Domain\Page\PageRepositoryInterface;
use App\Domain\Page\PageSlug;
use App\Domain\Page\PageTitle;
use App\Infrastructure\DataMapper\EntityHydratorInterface;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Query\QueryInterface;
use Yiisoft\ErrorHandler\Exception\UserException;

final readonly class DbPageRepository implements PageRepositoryInterface
{
    public function __construct(
        private EntityHydratorInterface $entityHydrator,
        private ConnectionInterface $db,
    ) {}

    /**
     * @throws UserException
     */
    public function getOrUserException(PageId $id): Page
    {
        /** @var Page */
        return $this->createQuery()->where(['id' => $id->toString()])->one()
            ?? throw new UserException('Page not found.');
    }

    public function hasBySlug(PageSlug $slug): bool
    {
        return $this->db->createQuery()
            ->from(Table::PAGE)
            ->where(['slug' => $slug->toString()])
            ->exists();
    }

    public function add(Page $page): void
    {
        $this->db->createCommand()
            ->insert(Table::PAGE, [
                'id' => $page->id->toString(),
                'title' => $page->title->toString(),
                'slug' => $page->slug->toString(),
                'content' => $page->content,
                'created_at' => $page->createdAt->format('Y-m-d H:i:s.uP'),
            ])
            ->execute();
    }

    private function createQuery(): QueryInterface
    {
        return $this->db->createQuery()
            ->from(Table::PAGE)
            ->select(['id', 'title', 'slug', 'content', 'created_at'])
            ->resultCallback(
                function (array $rows): array {
                    /**
                     * @var non-empty-list<array{
                     *     id: string,
                     *     title: non-empty-string,
                     *     slug: non-empty-string,
                     *     content: string,
                     *     created_at: string,
                     * }> $rows
                     */
                    return array_map(
                        fn(array $row) => $this->entityHydrator->create(
                            Page::class,
                            [
                                'id' => PageId::fromString($row['id']),
                                'title' => new PageTitle($row['title']),
                                'slug' => new PageSlug($row['slug']),
                                'content' => $row['content'],
                                'createdAt' => new DateTimeImmutable($row['created_at']),
                            ],
                        ),
                        $rows,
                    );
                },
            );
    }
}

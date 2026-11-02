<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\PageContent\Repository\PageContentRepositoryInterface;
use App\Domain\PageContent\Value\ContentPath;
use App\Domain\PageContent\Value\ElementPatch;
use App\Domain\PageContent\Value\PageKey;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class DbPageContentRepository implements PageContentRepositoryInterface
{
    public function __construct(
        private ConnectionInterface $db,
    ) {}

    public function patchElement(PageKey $key, ContentPath $path, ElementPatch $patch, DateTimeImmutable $now): void
    {
        $table = Table::PAGE_CONTENT;
        // One statement: the row is created on first edit, and only this path's element is merged, so
        // simultaneous edits of other paths of the same page are not overwritten.
        $this->db->createCommand(
            "INSERT INTO $table (page_key, content, updated_at)
             VALUES (:key, jsonb_build_object(CAST(:path AS text), CAST(:patch AS jsonb)), :now)
             ON CONFLICT (page_key) DO UPDATE SET
                content = $table.content || jsonb_build_object(
                    CAST(:path AS text),
                    COALESCE($table.content -> CAST(:path AS text), '{}'::jsonb) || CAST(:patch AS jsonb)
                ),
                updated_at = EXCLUDED.updated_at",
            [
                'key' => $key->toString(),
                'path' => $path->toString(),
                'patch' => json_encode($patch->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'now' => $now->format('Y-m-d H:i:s.uP'),
            ],
        )->execute();
    }
}

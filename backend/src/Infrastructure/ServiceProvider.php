<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\HtmlSanitizerInterface;
use App\Application\Shared\DocumentStorageInterface;
use App\Application\Shared\ImageStorageInterface;
use App\Application\Shared\PasswordHasherInterface;
use App\Application\Shared\RateLimiterInterface;
use App\Application\Auth\TokenIssuerInterface;
use App\Domain\Auth\Repository\LoginAttemptRepositoryInterface;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Order\Repository\OrderRequestLogInterface;
use App\Domain\PageContent\Repository\PageContentRepositoryInterface;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Environment;
use App\Infrastructure\DataMapper\EntityHydratorInterface;
use App\Infrastructure\DataMapper\PropertyReaderInterface;
use App\Infrastructure\DataMapper\ReflectionPropertyReader;
use App\Infrastructure\DataMapper\YiiHydratorEntityHydrator;
use App\Infrastructure\Repository\DbClientRepository;
use App\Infrastructure\Repository\DbLoginAttemptRepository;
use App\Infrastructure\Repository\DbOrderRepository;
use App\Infrastructure\Repository\DbOrderRequestLog;
use App\Infrastructure\Repository\DbPageContentRepository;
use App\Infrastructure\Repository\DbRefreshTokenRepository;
use App\Infrastructure\Repository\DbRoleRepository;
use App\Infrastructure\Repository\DbUserRepository;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\File\FileCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Migration\Service\MigrationService;
use Yiisoft\Db\Pgsql\Connection;
use Yiisoft\Db\Pgsql\Driver;
use Yiisoft\Db\Pgsql\Dsn;
use Yiisoft\Di\ServiceProviderInterface;

final readonly class ServiceProvider implements ServiceProviderInterface
{
    public function getDefinitions(): array
    {
        return [
            // Repositories
            PageContentRepositoryInterface::class => DbPageContentRepository::class,
            RoleRepositoryInterface::class => DbRoleRepository::class,
            UserRepositoryInterface::class => DbUserRepository::class,
            RefreshTokenRepositoryInterface::class => DbRefreshTokenRepository::class,
            LoginAttemptRepositoryInterface::class => DbLoginAttemptRepository::class,
            ClientRepositoryInterface::class => DbClientRepository::class,
            OrderRepositoryInterface::class => DbOrderRepository::class,
            OrderRequestLogInterface::class => DbOrderRequestLog::class,
            RateLimiterInterface::class => DbRateLimiter::class,
            HtmlSanitizerInterface::class => SymfonyHtmlSanitizer::class,
            ImageStorageInterface::class => static fn(Aliases $aliases) => new LocalImageStorage(self::uploadDir($aliases)),
            DocumentStorageInterface::class => static fn(Aliases $aliases) => new LocalDocumentStorage(self::uploadDir($aliases)),
            PasswordHasherInterface::class => Argon2idPasswordHasher::class,
            TokenIssuerInterface::class => static fn() => new JwtTokenIssuer(self::env('JWT_SECRET', '')),

            // DB (credentials come from the environment, see compose.yaml)
            ConnectionInterface::class => static fn(Aliases $aliases) => new Connection(
                new Driver(
                    (string) new Dsn(
                        host: self::env('DB_HOST', 'postgres'),
                        databaseName: Environment::isTest()
                            ? self::env('DB_TEST_NAME', 'app_test')
                            : self::env('DB_NAME', 'app'),
                        port: self::env('DB_PORT', '5432'),
                    ),
                    self::env('DB_USER', 'app'),
                    self::env('DB_PASSWORD', 'app'),
                ),
                new SchemaCache(
                    new FileCache($aliases->get('@runtime/cache/db')),
                ),
            ),
            MigrationService::class => [
                'setNewMigrationNamespace()' => ['App\\Infrastructure\\Migration'],
                'setSourceNamespaces()' => [['App\\Infrastructure\\Migration']],
            ],

            // Other
            EntityHydratorInterface::class => YiiHydratorEntityHydrator::class,
            PropertyReaderInterface::class => ReflectionPropertyReader::class,
        ];
    }

    public function getExtensions(): array
    {
        return [];
    }

    /**
     * Directory nginx serves at `/uploads/` (images at its top level, Documents under `files/`).
     */
    private static function uploadDir(Aliases $aliases): string
    {
        return Environment::isTest()
            ? $aliases->get('@runtime/test-uploads')
            : self::env('UPLOAD_DIR', $aliases->get('@root/storage/uploads'));
    }

    private static function env(string $name, string $default): string
    {
        $value = getenv($name);
        return $value === false || $value === '' ? $default : $value;
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\PasswordHasherInterface;
use App\Application\Auth\TokenIssuerInterface;
use App\Domain\Auth\Repository\LoginAttemptRepositoryInterface;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use App\Domain\Page\Repository\PageRepositoryInterface;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Environment;
use App\Infrastructure\DataMapper\EntityHydratorInterface;
use App\Infrastructure\DataMapper\PropertyReaderInterface;
use App\Infrastructure\DataMapper\ReflectionPropertyReader;
use App\Infrastructure\DataMapper\YiiHydratorEntityHydrator;
use App\Infrastructure\Repository\DbLoginAttemptRepository;
use App\Infrastructure\Repository\DbPageRepository;
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
            PageRepositoryInterface::class => DbPageRepository::class,
            RoleRepositoryInterface::class => DbRoleRepository::class,
            UserRepositoryInterface::class => DbUserRepository::class,
            RefreshTokenRepositoryInterface::class => DbRefreshTokenRepository::class,
            LoginAttemptRepositoryInterface::class => DbLoginAttemptRepository::class,
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

    private static function env(string $name, string $default): string
    {
        $value = getenv($name);
        return $value === false || $value === '' ? $default : $value;
    }
}

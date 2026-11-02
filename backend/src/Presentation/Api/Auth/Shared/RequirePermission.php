<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Shared;

use App\Domain\Role\Value\Permission;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;

/**
 * Rejects the request (403) unless the CurrentUser's Role holds the Permission. Must run after {@see Authenticate}.
 *
 * Usage on a route: `->middleware(Authenticate::class, RequirePermission::for(Permission::EditPage))`
 */
final readonly class RequirePermission implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Permission $permission,
    ) {}

    /**
     * Middleware definition for route configuration (the Permission is a constructor argument).
     *
     * @return array{class: class-string<self>, '__construct()': array{permission: Permission}}
     */
    public static function for(Permission $permission): array
    {
        return ['class' => self::class, '__construct()' => ['permission' => $permission]];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!Authenticate::currentUser($request)->role->has($this->permission)) {
            return $this->responseFactory->fail('Forbidden.', httpCode: Status::FORBIDDEN);
        }

        return $handler->handle($request);
    }
}

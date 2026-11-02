<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Shared;

use App\Application\Auth\AccessTokenResolver;
use App\Application\Auth\CurrentUser;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;

/**
 * Rejects requests without a valid Session (401), and (403) while the User must still change their password
 * (only /auth/change-password, /auth/me, /auth/logout and /auth/refresh work then; the last three do not use this
 * middleware). Otherwise exposes the CurrentUser, loaded fresh from the
 * database, as the request attribute {@see self::ATTRIBUTE}; read it with {@see self::currentUser()}.
 */
final readonly class Authenticate implements MiddlewareInterface
{
    public const string ATTRIBUTE = 'currentUser';

    public function __construct(
        private ResponseFactory $responseFactory,
        private SessionCookies $cookies,
        private AccessTokenResolver $resolver,
        private bool $enforcePasswordChange = true,
    ) {}

    /**
     * Middleware definition for the one route a User with a pending password change may still use.
     *
     * @return array{class: class-string<self>, '__construct()': array{enforcePasswordChange: false}}
     */
    public static function allowingPendingPasswordChange(): array
    {
        return ['class' => self::class, '__construct()' => ['enforcePasswordChange' => false]];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $current = $this->resolver->resolve($this->cookies->accessToken($request));
        if ($current === null) {
            return $this->responseFactory->fail('Not authenticated.', httpCode: Status::UNAUTHORIZED);
        }

        if ($this->enforcePasswordChange && $current->user->mustChangePassword) {
            return $this->responseFactory->fail(
                'You must change your password before continuing.',
                httpCode: Status::FORBIDDEN,
            );
        }

        return $handler->handle($request->withAttribute(self::ATTRIBUTE, $current));
    }

    public static function currentUser(ServerRequestInterface $request): CurrentUser
    {
        $current = $request->getAttribute(self::ATTRIBUTE);
        if (!$current instanceof CurrentUser) {
            throw new \LogicException('Route is missing the Authenticate middleware.');
        }

        return $current;
    }
}

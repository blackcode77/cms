<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Shared;

use App\Application\Auth\Session;
use App\Application\Auth\SessionIssuer;
use App\Application\Auth\TokenIssuerInterface;
use App\Environment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The Session travels in two httpOnly cookies: names are part of the contract with the frontend.
 */
final readonly class SessionCookies
{
    public const string ACCESS = 'access_token';
    public const string REFRESH = 'refresh_token';

    public function attach(ResponseInterface $response, Session $session): ResponseInterface
    {
        return $response
            ->withAddedHeader('Set-Cookie', self::cookie(self::ACCESS, $session->accessToken, TokenIssuerInterface::ACCESS_TTL_SECONDS))
            ->withAddedHeader('Set-Cookie', self::cookie(self::REFRESH, $session->refreshToken, SessionIssuer::REFRESH_TTL_SECONDS));
    }

    public function clear(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withAddedHeader('Set-Cookie', self::cookie(self::ACCESS, '', 0))
            ->withAddedHeader('Set-Cookie', self::cookie(self::REFRESH, '', 0));
    }

    public function accessToken(ServerRequestInterface $request): ?string
    {
        return self::read($request, self::ACCESS);
    }

    public function refreshToken(ServerRequestInterface $request): ?string
    {
        return self::read($request, self::REFRESH);
    }

    private static function read(ServerRequestInterface $request, string $name): ?string
    {
        $value = $request->getCookieParams()[$name] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    private static function cookie(string $name, string $value, int $maxAge): string
    {
        return sprintf(
            '%s=%s; Max-Age=%d; Path=/; HttpOnly; SameSite=Lax%s',
            $name,
            $value,
            $maxAge,
            Environment::isDev() ? '' : '; Secure',
        );
    }
}

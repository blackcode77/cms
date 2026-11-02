<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Shared;

use Psr\Http\Message\ServerRequestInterface;

/**
 * The client address used for rate limiting. The API is only reachable through our nginx, which overwrites
 * `X-Real-IP` with the real peer address (docker/nginx/default.conf), so the header is trusted; REMOTE_ADDR
 * (the proxy itself) is the fallback when it is missing, e.g. in console or test contexts.
 * If another proxy is put in front of nginx, configure nginx's realip module so `$remote_addr` stays the client.
 */
final class ClientIp
{
    public static function fromRequest(ServerRequestInterface $request): string
    {
        $candidates = [
            $request->getHeaderLine('X-Real-IP'),
            $request->getServerParams()['REMOTE_ADDR'] ?? '',
        ];
        foreach ($candidates as $candidate) {
            if (is_string($candidate) && filter_var(trim($candidate), FILTER_VALIDATE_IP) !== false) {
                return trim($candidate);
            }
        }

        return 'unknown';
    }
}

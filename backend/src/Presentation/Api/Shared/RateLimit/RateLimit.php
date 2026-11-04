<?php

declare(strict_types=1);

namespace App\Presentation\Api\Shared\RateLimit;

use App\Application\Shared\RateLimiterInterface;
use App\Presentation\Api\Auth\Shared\ClientIp;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;

/**
 * Rejects (429) requests beyond a number per minute from one client address to one route.
 *
 * Usage on a route: `->middleware(RateLimit::perMinute(10))`
 */
final readonly class RateLimit implements MiddlewareInterface
{
    private const int WINDOW_SECONDS = 60;

    /**
     * @param positive-int $limit
     */
    public function __construct(
        private RateLimiterInterface $limiter,
        private ResponseFactory $responseFactory,
        private ClockInterface $clock,
        private int $limit,
    ) {}

    /**
     * Middleware definition for route configuration (the limit is a constructor argument).
     *
     * @param positive-int $limit
     * @return array{class: class-string<self>, '__construct()': array{limit: positive-int}}
     */
    public static function perMinute(int $limit): array
    {
        return ['class' => self::class, '__construct()' => ['limit' => $limit]];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $key = ClientIp::fromRequest($request) . ' ' . $request->getMethod() . ' ' . $request->getUri()->getPath();
        $now = DateTimeImmutable::createFromInterface($this->clock->now());

        if (!$this->limiter->hit($key, $this->limit, self::WINDOW_SECONDS, $now)) {
            return $this->responseFactory
                ->fail('Слишком много запросов, попробуйте позже.', httpCode: Status::TOO_MANY_REQUESTS)
                ->withHeader('Retry-After', (string) (self::WINDOW_SECONDS - $now->getTimestamp() % self::WINDOW_SECONDS));
        }

        return $handler->handle($request);
    }
}

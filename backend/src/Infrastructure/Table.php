<?php

declare(strict_types=1);

namespace App\Infrastructure;

final readonly class Table
{
    public const string PAGE_CONTENT = 'page_content';
    public const string ROLES = 'roles';
    public const string ROLE_PERMISSIONS = 'role_permissions';
    public const string USERS = 'users';
    public const string REFRESH_TOKENS = 'refresh_tokens';
    public const string LOGIN_ATTEMPTS = 'login_attempts';
    public const string CLIENTS = 'clients';
    public const string ORDERS = 'orders';
    public const string ORDER_REQUEST_LOG = 'order_request_log';
    public const string RATE_LIMIT_HITS = 'rate_limit_hits';
}

<?php

declare(strict_types=1);

namespace App\Domain\Role\Value;

/**
 * Fixed catalog of capabilities. Roles are granted Permissions; they cannot be invented at runtime.
 */
enum Permission: string
{
    case ViewDashboard = 'view_dashboard';
    case EditPage = 'edit_page';
    case ViewUsers = 'view_users';
    case ManageUsers = 'manage_users';
    case ViewRoles = 'view_roles';
    case ManageRoles = 'manage_roles';
    case ViewOrders = 'view_orders';
    case ManageOrders = 'manage_orders';
    /** Makes the User eligible to be the Manager of an Order. */
    case HandleOrders = 'handle_orders';
    case ViewClients = 'view_clients';
    case ManageClients = 'manage_clients';
}

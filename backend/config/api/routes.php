<?php

declare(strict_types=1);

use App\Domain\Role\Value\Permission;
use App\Presentation\Api;
use App\Presentation\Api\Auth\Shared\Authenticate;
use App\Presentation\Api\Auth\Shared\RequirePermission;
use App\Presentation\Api\Shared\RateLimit\RateLimit;
use Yiisoft\Router\Route;

return [
    Route::get('/health')->action(Api\Health\Action::class),
    Route::get('/page-content')->action(Api\PageContent\Read\Action::class),
    Route::post('/orders/request')
        ->middleware(RateLimit::perMinute(10))
        ->action(Api\Order\Request\Action::class),
    Route::post('/auth/login')->action(Api\Auth\Login\Action::class),
    Route::post('/auth/refresh')->action(Api\Auth\Refresh\Action::class),
    Route::post('/auth/logout')->action(Api\Auth\Logout\Action::class),
    Route::post('/auth/change-password')
        ->middleware(Authenticate::allowingPendingPasswordChange())
        ->action(Api\Auth\ChangePassword\Action::class),
    Route::get('/auth/me')->action(Api\Auth\Me\Action::class),
    Route::patch('/page-content/{key}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::EditPage))
        ->action(Api\PageContent\Update\Action::class),
    Route::post('/uploads/image')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::EditPage))
        ->action(Api\PageContent\UploadImage\Action::class),
    Route::post('/uploads/file')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::EditPage))
        ->action(Api\PageContent\UploadDocument\Action::class),
    Route::get('/users')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewUsers))
        ->action(Api\User\List\Action::class),
    Route::get('/users/assignable-roles')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewUsers))
        ->action(Api\User\AssignableRoles\Action::class),
    Route::post('/users')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageUsers))
        ->action(Api\User\Create\Action::class),
    Route::put('/users/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageUsers))
        ->action(Api\User\Update\Action::class),
    Route::delete('/users/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageUsers))
        ->action(Api\User\Delete\Action::class),
    Route::post('/users/{id}/reset-password')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageUsers))
        ->action(Api\User\ResetPassword\Action::class),
    Route::get('/roles')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewRoles))
        ->action(Api\Role\List\Action::class),
    Route::get('/permissions')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewRoles))
        ->action(Api\Role\Permissions\Action::class),
    Route::post('/roles')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageRoles))
        ->action(Api\Role\Create\Action::class),
    Route::put('/roles/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageRoles))
        ->action(Api\Role\Update\Action::class),
    Route::delete('/roles/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageRoles))
        ->action(Api\Role\Delete\Action::class),
    Route::get('/orders')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewOrders))
        ->action(Api\Order\List\Action::class),
    Route::get('/orders/managers')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewOrders))
        ->action(Api\Order\Managers\Action::class),
    Route::get('/orders/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewOrders))
        ->action(Api\Order\View\Action::class),
    Route::post('/orders')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageOrders))
        ->action(Api\Order\Create\Action::class),
    Route::put('/orders/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageOrders))
        ->action(Api\Order\Update\Action::class),
    Route::delete('/orders/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageOrders))
        ->action(Api\Order\Delete\Action::class),
    Route::get('/clients')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewClients))
        ->action(Api\Client\List\Action::class),
    Route::get('/clients/by-phone')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewClients))
        ->action(Api\Client\ByPhone\Action::class),
    Route::get('/clients/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewClients))
        ->action(Api\Client\View\Action::class),
    Route::post('/clients')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageClients))
        ->action(Api\Client\Create\Action::class),
    Route::put('/clients/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageClients))
        ->action(Api\Client\Update\Action::class),
    Route::delete('/clients/{id}')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageClients))
        ->action(Api\Client\Delete\Action::class),
];

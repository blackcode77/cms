<?php

declare(strict_types=1);

use App\Domain\Role\Value\Permission;
use App\Presentation\Api;
use App\Presentation\Api\Auth\Shared\Authenticate;
use App\Presentation\Api\Auth\Shared\RequirePermission;
use Yiisoft\Router\Route;

return [
    Route::get('/health')->action(Api\Health\Action::class),
    Route::get('/pages')->action(Api\Page\List\Action::class),
    Route::post('/auth/login')->action(Api\Auth\Login\Action::class),
    Route::post('/auth/refresh')->action(Api\Auth\Refresh\Action::class),
    Route::post('/auth/logout')->action(Api\Auth\Logout\Action::class),
    Route::get('/auth/me')->action(Api\Auth\Me\Action::class),
    Route::post('/pages')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::EditPage))
        ->action(Api\Page\Create\Action::class),
    Route::get('/users')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewUsers))
        ->action(Api\User\List\Action::class),
    Route::get('/users/assignable-roles')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ViewUsers))
        ->action(Api\User\AssignableRoles\Action::class),
    Route::post('/users')
        ->middleware(Authenticate::class, RequirePermission::for(Permission::ManageUsers))
        ->action(Api\User\Create\Action::class),
];

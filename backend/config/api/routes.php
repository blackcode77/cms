<?php

declare(strict_types=1);

use App\Presentation\Api;
use Yiisoft\Router\Route;

return [
    Route::get('/health')->action(Api\Health\Action::class),
    Route::get('/pages')->action(Api\Page\List\Action::class),
    Route::post('/pages')->action(Api\Page\Create\Action::class),
];

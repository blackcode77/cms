<?php

declare(strict_types=1);

use App\Environment;
use Psr\Log\LogLevel;
use Yiisoft\ErrorHandler\ErrorHandler;
use Yiisoft\ErrorHandler\Renderer\JsonRenderer;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileTarget;
use Yiisoft\Yii\Runner\Http\HttpApplicationRunner;

$root = dirname(__DIR__);

require_once $root . '/src/autoload.php';

// Only the API is served by PHP; everything else belongs to the Nuxt frontend (see docker/nginx).
new HttpApplicationRunner(
    rootPath: $root,
    debug: Environment::appDebug(),
    checkEvents: Environment::appDebug(),
    environment: Environment::appEnv(),
    bootstrapGroup: 'bootstrap-api',
    eventsGroup: 'events-api',
    diGroup: 'di-api',
    diProvidersGroup: 'di-providers-api',
    diDelegatesGroup: 'di-delegates-api',
    diTagsGroup: 'di-tags-api',
    paramsGroup: 'params-api',
    nestedParamsGroups: ['params', 'params-web'],
    nestedEventsGroups: ['events', 'events-web'],
    temporaryErrorHandler: new ErrorHandler(
        new Logger([
            new FileTarget($root . '/runtime/logs/api-container-building.log')
                ->setLevels([LogLevel::EMERGENCY, LogLevel::ERROR, LogLevel::WARNING]),
        ]),
        new JsonRenderer(),
    ),
)->run();

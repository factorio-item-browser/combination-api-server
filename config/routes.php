<?php

/**
 * The file providing the routes.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
// phpcs:ignoreFile

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use FactorioItemBrowser\CombinationApi\Server\Constant\RouteName;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/status', Handler\Combination\StatusHandler::class, RouteName::COMBINATION_STATUS);
    $app->post('/validate/{factorio-version}', Handler\Combination\ValidateHandler::class, RouteName::COMBINATION_VALIDATE);

    $app->post('/job', Handler\Job\CreateHandler::class, RouteName::JOB_CREATE);
    $app->get('/job/{job-id}', Handler\Job\DetailsHandler::class, RouteName::JOB_DETAILS);
    $app->patch('/job/{job-id}', Handler\Job\UpdateHandler::class, RouteName::JOB_UPDATE);
    $app->get('/jobs', Handler\Job\ListHandler::class, RouteName::JOB_LIST);
};

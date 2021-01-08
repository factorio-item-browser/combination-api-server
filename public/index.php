<?php

/**
 * The entry point for handling the HTTP requests.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

ini_set('serialize_precision', '-1');
chdir(dirname(__DIR__));
require(__DIR__ . '/../vendor/autoload.php');

(function (): void {
    /* @var ContainerInterface $container */
    $container = require(__DIR__ . '/../config/container.php');

    /* @var Application $app */
    $app = $container->get(Application::class);
    $factory = $container->get(MiddlewareFactory::class);

    (require(__DIR__ . '/../config/pipeline.php'))($app, $factory, $container);
    (require(__DIR__ . '/../config/routes.php'))($app, $factory, $container);

    $app->run();
})();

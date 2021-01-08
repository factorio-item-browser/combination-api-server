#!/usr/bin/env php
<?php

/**
 * The script for building up the config caches.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use Psr\Container\ContainerInterface;

chdir(dirname(__DIR__));
require(__DIR__ . '/../vendor/autoload.php');

(function (): void {
    /** @var ContainerInterface $container */
    $container = require(__DIR__ . '/../config/container.php');
    $config = $container->get('config');

    foreach (array_keys($config['dependencies']['factories'] ?? []) as $alias) {
        $container->get((string) $alias);
    }
})();

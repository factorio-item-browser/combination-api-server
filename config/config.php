<?php

/**
 * The main configuration file.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$cacheConfig = [
    'config_cache_path' => 'data/cache/config-cache.php',
];

$aggregator = new ConfigAggregator([
    // Include cache configuration
    new ArrayProvider($cacheConfig),

    \BluePsyduck\FactorioModPortalClient\ConfigProvider::class,
    \BluePsyduck\MapperManager\ConfigProvider::class,
    \FactorioItemBrowser\CombinationApi\Client\ConfigProvider::class,
    \Laminas\HttpHandlerRunner\ConfigProvider::class,
    \Mezzio\ConfigProvider::class,
    \Mezzio\Helper\ConfigProvider::class,
    \Mezzio\Router\ConfigProvider::class,
    \Mezzio\Router\FastRouteRouter\ConfigProvider::class,

    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `*.global.php`
    //   - `[FIB_ENV]/*.local.php`
    new PhpFileProvider(
        realpath(__DIR__) . sprintf('/autoload/{*.global.php,%s/*.local.php}', getenv('FIB_ENV') ?: 'production'),
    ),
], $cacheConfig['config_cache_path']);

return $aggregator->getMergedConfig();

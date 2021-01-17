<?php

/**
 * The configuration for the Combination API server itself.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use FactorioItemBrowser\CombinationApi\Client\Request\Job\CreateRequest;
use FactorioItemBrowser\CombinationApi\Client\Request\Job\UpdateRequest;
use FactorioItemBrowser\CombinationApi\Server\Constant\ConfigKey;
use FactorioItemBrowser\CombinationApi\Server\Constant\RouteName;

return [
    ConfigKey::MAIN => [
        ConfigKey::REQUEST_CLASSES_BY_ROUTES => [
            RouteName::JOB_CREATE => CreateRequest::class,
            RouteName::JOB_UPDATE => UpdateRequest::class,
        ],
    ],
];

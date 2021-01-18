<?php

/**
 * The configuration for the Combination API server itself.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use FactorioItemBrowser\CombinationApi\Server\Constant\ConfigKey;

return [
    ConfigKey::MAIN => [
        ConfigKey::AGENTS => [
            [
                ConfigKey::AGENT_NAME => 'development',
                ConfigKey::AGENT_ACCESS_KEY => 'factorio-item-browser',
                ConfigKey::AGENT_CAN_CREATE_JOBS => true,
                ConfigKey::AGENT_CAN_UPDATE_JOBS => true,
            ],
        ],
    ],
];

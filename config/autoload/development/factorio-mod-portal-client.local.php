<?php

/**
 * The configuration of the Factorio Mod Portal Client.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use BluePsyduck\FactorioModPortalClient\Constant\ConfigKey;

return [
    ConfigKey::MAIN => [
        ConfigKey::OPTIONS => [
            ConfigKey::OPTION_USERNAME => '',
            ConfigKey::OPTION_TOKEN => '',
        ],
    ],
];

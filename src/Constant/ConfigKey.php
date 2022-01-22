<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Constant;

/**
 * The interface holding the keys used in the config.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface ConfigKey
{
    public const MAIN = 'combination-api-server';
    public const ALLOWED_ORIGINS = 'allowed-origins';
    public const REQUEST_CLASSES_BY_ROUTES = 'request-classes-by-routes';

    public const AGENTS = 'agents';
    public const AGENT_NAME = 'name';
    public const AGENT_ACCESS_KEY = 'access-key';
    public const AGENT_CAN_CREATE_JOBS = 'can-create-jobs';
    public const AGENT_CAN_UPDATE_JOBS = 'can-update-jobs';

    public const TRACKING = 'tracking';
    public const TRACKING_MEASUREMENT_ID = 'measurement-id';
    public const TRACKING_API_SECRET = 'api-secret';
}

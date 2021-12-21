<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Tracking\Event;

use BluePsyduck\Ga4MeasurementProtocol\Attribute\Event;
use BluePsyduck\Ga4MeasurementProtocol\Attribute\Parameter;
use BluePsyduck\Ga4MeasurementProtocol\Request\Event\EventInterface;

/**
 * The event representing a request to the API.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @codeCoverageIgnore -- Should not need any coverage to begin with
 */
#[Event('request')]
class RequestEvent implements EventInterface
{
    /**
     * The name of the agent which initiated the request.
     */
    #[Parameter('agent_name')]
    public ?string $agentName = null;

    /**
     * The name of the matched route for the request.
     */
    #[Parameter('route_name')]
    public ?string $routeName = null;

    /**
     * The runtime of the request, in milliseconds.
     */
    #[Parameter('runtime')]
    public ?float $runtime = null;

    /**
     * The status code the request resulted in.
     */
    #[Parameter('status_code')]
    public ?int $statusCode = null;
}

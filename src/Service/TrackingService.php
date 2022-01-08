<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use BluePsyduck\Ga4MeasurementProtocol\ClientInterface;
use BluePsyduck\Ga4MeasurementProtocol\Request\Event\EventInterface;
use BluePsyduck\Ga4MeasurementProtocol\Request\Payload;
use Psr\Http\Client\ClientExceptionInterface;
use Ramsey\Uuid\Uuid;

/**
 * The service handling the tracking.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TrackingService
{
    /** @var array<EventInterface> */
    private array $events = [];

    public function __construct(
        private readonly ClientInterface $client,
    ) {
    }

    public function addEvent(EventInterface $event): void
    {
        $this->events[] = $event;
    }

    public function track(): void
    {
        try {
            $payload = new Payload();
            $payload->clientId = Uuid::uuid4()->toString();
            $payload->events = $this->events;

            $this->client->send($payload);
            $this->events = [];
        } catch (ClientExceptionInterface) {
            // Ignore any errors with tracking.
        }
    }
}

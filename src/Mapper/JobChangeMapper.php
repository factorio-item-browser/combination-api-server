<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Mapper;

use BluePsyduck\MapperManager\Mapper\StaticMapperInterface;
use FactorioItemBrowser\CombinationApi\Client\Transfer\JobChange as ClientJobChange;
use FactorioItemBrowser\CombinationApi\Server\Entity\JobChange as DatabaseJobChange;

/**
 * The mapper for the job changes.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements StaticMapperInterface<DatabaseJobChange, ClientJobChange>
 */
class JobChangeMapper implements StaticMapperInterface
{
    public function getSupportedSourceClass(): string
    {
        return DatabaseJobChange::class;
    }

    public function getSupportedDestinationClass(): string
    {
        return ClientJobChange::class;
    }

    /**
     * @param DatabaseJobChange $source
     * @param ClientJobChange $destination
     */
    public function map(object $source, object $destination): void
    {
        $destination->initiator = $source->getInitiator();
        $destination->status = $source->getStatus();
        $destination->timestamp = $source->getTimestamp();
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Mapper;

use BluePsyduck\MapperManager\Mapper\DynamicMapperInterface;
use BluePsyduck\MapperManager\MapperManagerAwareInterface;
use BluePsyduck\MapperManager\MapperManagerAwareTrait;
use FactorioItemBrowser\CombinationApi\Client\Transfer\Job as ClientJob;
use FactorioItemBrowser\CombinationApi\Client\Transfer\JobChange as ClientJobChange;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job as DatabaseJob;
use FactorioItemBrowser\CombinationApi\Server\Entity\JobChange as DatabaseJobChange;

/**
 * The mapper of the job.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements DynamicMapperInterface<DatabaseJob, ClientJob>
 */
class JobMapper implements DynamicMapperInterface, MapperManagerAwareInterface
{
    use MapperManagerAwareTrait;

    public function supports(object $source, object $destination): bool
    {
        return $source instanceof DatabaseJob && $destination instanceof ClientJob;
    }

    /**
     * @param DatabaseJob $source
     * @param ClientJob $destination
     */
    public function map(object $source, object $destination): void
    {
        $destination->id = $source->getId()->toString();
        $destination->combinationId = $source->getCombination()->getId()->toString();
        $destination->priority = $source->getPriority();
        $destination->status = $source->getStatus();
        $destination->errorMessage = $source->getErrorMessage();
        $destination->creationTime = $source->getCreationTime();
        $destination->changes = array_map(function (DatabaseJobChange $change): ClientJobChange {
            return $this->mapperManager->map($change, new ClientJobChange());
        }, $source->getChanges()->toArray());
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Mapper;

use BluePsyduck\MapperManager\Mapper\DynamicMapperInterface;
use FactorioItemBrowser\CombinationApi\Client\Transfer\Combination as ClientCombination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination as DatabaseCombination;
use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;

/**
 * The mapper for the combination.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements DynamicMapperInterface<DatabaseCombination, ClientCombination>
 */
class CombinationMapper implements DynamicMapperInterface
{
    private CombinationIdCalculator $combinationIdCalculator;

    public function __construct(CombinationIdCalculator $combinationIdCalculator)
    {
        $this->combinationIdCalculator = $combinationIdCalculator;
    }

    public function supports(object $source, object $destination): bool
    {
        return $source instanceof DatabaseCombination && $destination instanceof ClientCombination;
    }

    /**
     * @param DatabaseCombination $source
     * @param ClientCombination $destination
     */
    public function map(object $source, object $destination): void
    {
        $destination->id = $source->getId()->toString();
        $destination->shortId = $this->combinationIdCalculator->toShortId($source->getId());
        $destination->modNames = $source->getModNames();
        $destination->isDataAvailable = $source->getExportTime() !== null;
        $destination->exportTime = $source->getExportTime();
    }
}

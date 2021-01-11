<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Mapper;

use BluePsyduck\MapperManager\Mapper\StaticMapperInterface;
use FactorioItemBrowser\CombinationApi\Client\Response\StatusResponse;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;

/**
 * The mapper for the status response.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements StaticMapperInterface<Combination, StatusResponse>
 */
class StatusResponseMapper implements StaticMapperInterface
{
    private CombinationIdCalculator $combinationIdCalculator;

    public function __construct(CombinationIdCalculator $combinationIdCalculator)
    {
        $this->combinationIdCalculator = $combinationIdCalculator;
    }

    public function getSupportedSourceClass(): string
    {
        return Combination::class;
    }

    public function getSupportedDestinationClass(): string
    {
        return StatusResponse::class;
    }

    /**
     * @param Combination $source
     * @param StatusResponse $destination
     */
    public function map(object $source, object $destination): void
    {
        $destination->id = $source->getId()->toString();
        $destination->shortId = $this->combinationIdCalculator->toShortId($source->getId());
        $destination->modNames = $source->getModNames();
        $destination->isDataAvailable = false;
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;
use FactorioItemBrowser\CombinationApi\Server\Repository\CombinationRepository;

/**
 * The service handling the combinations.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CombinationService
{
    private CombinationIdCalculator $combinationIdCalculator;
    private CombinationRepository $combinationRepository;
    private ModService $modService;

    public function __construct(
        CombinationIdCalculator $combinationIdCalculator,
        CombinationRepository $combinationRepository,
        ModService $modService
    ) {
        $this->combinationIdCalculator = $combinationIdCalculator;
        $this->combinationRepository = $combinationRepository;
        $this->modService = $modService;
    }

    /**
     * Returns the combination representing the specified mods.
     * @param array<string> $modNames
     * @return Combination
     */
    public function getCombinationByModNames(array $modNames): Combination
    {
        $combinationId = $this->combinationIdCalculator->fromModNames($modNames);
        $combination = $this->combinationRepository->findById($combinationId);
        if ($combination !== null) {
            return $combination;
        }

        $mods = $this->modService->getMods($modNames);
        return $this->combinationRepository->create($combinationId, $mods);
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use FactorioItemBrowser\CombinationApi\Client\Constant\HeaderName;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Exception\MissingCombinationHeaderException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Exception\UnknownCombinationException;
use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;
use FactorioItemBrowser\CombinationApi\Server\Repository\CombinationRepository;
use Psr\Http\Message\RequestInterface;
use Ramsey\Uuid\UuidInterface;

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
     * Returns the combination with the specified id.
     * @param UuidInterface $combinationId
     * @return Combination
     * @throws ServerException
     */
    public function getCombinationById(UuidInterface $combinationId): Combination
    {
        $combination = $this->combinationRepository->findById($combinationId);
        if ($combination === null) {
            throw new UnknownCombinationException($combinationId);
        }
        return $combination;
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

    /**
     * Returns the combination from a value of the request.
     * @param string $combinationId
     * @return Combination
     * @throws ServerException
     */
    public function getCombinationFromRequestValue(string $combinationId): Combination
    {
        return $this->getCombinationById($this->combinationIdCalculator->fromId($combinationId));
    }

    /**
     * Returns the combination from the request headers.
     * @param RequestInterface $request
     * @return Combination
     * @throws ServerException
     */
    public function getCombinationFromRequestHeader(RequestInterface $request): Combination
    {
        if ($request->hasHeader(HeaderName::COMBINATION_ID)) {
            $id = $request->getHeaderLine(HeaderName::COMBINATION_ID);
            return $this->getCombinationById($this->combinationIdCalculator->fromId($id));
        }

        if ($request->hasHeader(HeaderName::SHORT_COMBINATION_ID)) {
            $shortId = $request->getHeaderLine(HeaderName::SHORT_COMBINATION_ID);
            return $this->getCombinationById($this->combinationIdCalculator->fromShortId($shortId));
        }

        if ($request->hasHeader(HeaderName::MOD_NAMES)) {
            $modNames = explode(',', $request->getHeaderLine(HeaderName::MOD_NAMES));
            $modNames = array_map('trim', $modNames);
            return $this->getCombinationByModNames($modNames);
        }

        throw new MissingCombinationHeaderException();
    }
}

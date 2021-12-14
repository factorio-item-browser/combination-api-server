<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Handler\Combination;

use BluePsyduck\FactorioModPortalClient\Entity\Version;
use FactorioItemBrowser\CombinationApi\Client\Response\Combination\ValidateResponse;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\CombinationService;
use FactorioItemBrowser\CombinationApi\Server\Service\ValidationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The handler for the validate endpoint.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ValidateHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly CombinationService $combinationService,
        private readonly ValidationService $validationService,
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ServerException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $combination = $this->combinationService->getCombinationFromRequestHeader($request);
        $factorioVersion = new Version($request->getAttribute('factorio-version'));

        $response = new ValidateResponse();
        $response->mods = $this->validationService->validate($combination->getModNames(), $factorioVersion);

        $response->isValid = true;
        foreach ($response->mods as $mod) {
            if (count($mod->problems) > 0) {
                $response->isValid = false;
                break;
            }
        }

        return new ClientResponse($response);
    }
}

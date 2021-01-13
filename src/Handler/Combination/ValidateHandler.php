<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Handler\Combination;

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
    private CombinationService $combinationService;
    private ValidationService $validationService;

    public function __construct(CombinationService $combinationService, ValidationService $validationService)
    {
        $this->combinationService = $combinationService;
        $this->validationService = $validationService;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ServerException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $combination = $this->combinationService->getCombinationFromRequestHeader($request);

        $response = new ValidateResponse();
        $response->mods = $this->validationService->validate($combination->getModNames());

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

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Response;

use BluePsyduck\LaminasAutoWireFactory\Attribute\ReadConfig;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * The class generating the error response.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ErrorResponseGenerator
{
    public function __construct(
        private readonly LoggerInterface $errorLogger,
        #[ReadConfig('debug')]
        private readonly bool $debug,
    ) {
    }

    public function __invoke(Throwable $exception): ResponseInterface
    {
        $statusCode = $exception instanceof ServerException ? $exception->getCode() : 500;
        if (floor($statusCode / 100) === 5.) {
            $this->errorLogger->crit($exception);
        }

        if ($this->debug) {
            $errorResponse = [
                'error' => [
                    'message' => $exception->getMessage(),
                    'backtrace' => $exception->getTrace(),
                ],
            ];
        } else {
            $message = $exception instanceof ServerException ? $exception->getMessage() : 'Internal server error';
            $errorResponse = [
                'error' => [
                    'message' => $message,
                ],
            ];
        }

        return new JsonResponse($errorResponse, $statusCode);
    }
}

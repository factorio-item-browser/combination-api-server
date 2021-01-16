<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Response;

use Exception;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Response\ErrorResponseGenerator;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the ErrorResponseGenerator class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Response\ErrorResponseGenerator
 */
class ErrorResponseGeneratorTest extends TestCase
{
    /**
     * Provides the data for the invoke test.
     * @return array<mixed>
     */
    public function provideInvoke(): array
    {
        $exception1 = new ServerException('test exception', 512);
        $exception2 = new ServerException('test exception', 420);
        $exception3 = new Exception('test exception', 512);

        return [
            [$exception1, false, true, 512, ['error' => ['message' => 'test exception']]],
            [$exception2, false, false, 420, ['error' => ['message' => 'test exception']]],
            [$exception3, false, true, 500, ['error' => ['message' => 'Internal server error']]],

            [$exception1, true, true, 512, ['error' => [
                'message' => 'test exception',
                'backtrace' => $exception1->getTrace(),
            ]]],
        ];
    }

    /**
     * @param Throwable $exception
     * @param bool $debug
     * @param bool $expectLog
     * @param int $expectedStatusCode
     * @param array<mixed> $expectedPayload
     * @dataProvider provideInvoke
     */
    public function testInvoke(
        Throwable $exception,
        bool $debug,
        bool $expectLog,
        int $expectedStatusCode,
        array $expectedPayload
    ): void {
        $errorLogger = $this->createMock(LoggerInterface::class);
        $errorLogger->expects($expectLog ? $this->once() : $this->never())
                    ->method('crit')
                    ->with($this->identicalTo($exception));

        $instance = new ErrorResponseGenerator($errorLogger, $debug);
        $response = $instance($exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        /* @var JsonResponse $response  */
        $this->assertSame($expectedStatusCode, $response->getStatusCode());
        $this->assertSame($expectedPayload, $response->getPayload());
    }
}

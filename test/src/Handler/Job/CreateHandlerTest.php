<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\CombinationApi\Client\Request\Job\CreateRequest;
use FactorioItemBrowser\CombinationApi\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Handler\Job\CreateHandler;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\CombinationService;
use FactorioItemBrowser\CombinationApi\Server\Service\JobService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The PHPUnit test of the CreateHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Handler\Job\CreateHandler
 */
class CreateHandlerTest extends TestCase
{
    /**
     * @throws ServerException
     */
    public function testHandle(): void
    {
        $combinationId = 'abc';
        $priority = 'def';
        $job = $this->createMock(Job::class);
        $detailsResponse = $this->createMock(DetailsResponse::class);

        $combination = $this->createMock(Combination::class);
        $combination->expects($this->once())
                    ->method('getUnfinishedJob')
                    ->willReturn(null);

        $clientRequest = new CreateRequest();
        $clientRequest->combinationId = $combinationId;
        $clientRequest->priority = $priority;

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getParsedBody')
                ->willReturn($clientRequest);

        $combinationService = $this->createMock(CombinationService::class);
        $combinationService->expects($this->once())
                           ->method('getCombinationFromRequestValue')
                           ->with($this->identicalTo($combinationId))
                           ->willReturn($combination);

        $jobService = $this->createMock(JobService::class);
        $jobService->expects($this->once())
                   ->method('createJobForCombination')
                   ->with($this->identicalTo($combination), $this->identicalTo($priority))
                   ->willReturn($job);

        $mapperManager = $this->createMock(MapperManagerInterface::class);
        $mapperManager->expects($this->once())
                      ->method('map')
                      ->with($this->identicalTo($job), $this->isInstanceOf(DetailsResponse::class))
                      ->willReturn($detailsResponse);

        $instance = new CreateHandler($combinationService, $jobService, $mapperManager);
        $result = $instance->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
        /** @var ClientResponse $result */
        $this->assertSame($detailsResponse, $result->getPayload());
    }

    /**
     * @throws ServerException
     */
    public function testHandleWithExistingJob(): void
    {
        $combinationId = 'abc';
        $priority = 'def';
        $unfinishedJob = $this->createMock(Job::class);
        $detailsResponse = $this->createMock(DetailsResponse::class);

        $combination = $this->createMock(Combination::class);
        $combination->expects($this->once())
                    ->method('getUnfinishedJob')
                    ->willReturn($unfinishedJob);

        $clientRequest = new CreateRequest();
        $clientRequest->combinationId = $combinationId;
        $clientRequest->priority = $priority;

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getParsedBody')
                ->willReturn($clientRequest);

        $combinationService = $this->createMock(CombinationService::class);
        $combinationService->expects($this->once())
                           ->method('getCombinationFromRequestValue')
                           ->with($this->identicalTo($combinationId))
                           ->willReturn($combination);

        $jobService = $this->createMock(JobService::class);
        $jobService->expects($this->never())
                   ->method('createJobForCombination');

        $mapperManager = $this->createMock(MapperManagerInterface::class);
        $mapperManager->expects($this->once())
                      ->method('map')
                      ->with($this->identicalTo($unfinishedJob), $this->isInstanceOf(DetailsResponse::class))
                      ->willReturn($detailsResponse);

        $instance = new CreateHandler($combinationService, $jobService, $mapperManager);
        $result = $instance->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
        /** @var ClientResponse $result */
        $this->assertSame($detailsResponse, $result->getPayload());
    }
}

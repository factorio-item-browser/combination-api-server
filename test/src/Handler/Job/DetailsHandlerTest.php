<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\CombinationApi\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Handler\Job\DetailsHandler;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\JobService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The PHPUnit test of the DetailsHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Handler\Job\DetailsHandler
 */
class DetailsHandlerTest extends TestCase
{
    /**
     * @throws ServerException
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle(): void
    {
        $jobId = 'abc';
        $job = $this->createMock(Job::class);
        $detailsResponse = $this->createMock(DetailsResponse::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->any())
                 ->method('getAttribute')
                 ->with($this->identicalTo('job-id'))
                 ->willReturn($jobId);

        $jobService = $this->createMock(JobService::class);
        $jobService->expects($this->once())
                   ->method('getJobFromRequestValue')
                   ->with($this->identicalTo($jobId))
                   ->willReturn($job);

        $mapperManager = $this->createMock(MapperManagerInterface::class);
        $mapperManager->expects($this->once())
                      ->method('map')
                      ->with($this->identicalTo($job), $this->isInstanceOf(DetailsResponse::class))
                      ->willReturn($detailsResponse);

        $instance = new DetailsHandler($jobService, $mapperManager);
        $result = $instance->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
        /** @var ClientResponse $result */
        $this->assertSame($detailsResponse, $result->getPayload());
    }
}

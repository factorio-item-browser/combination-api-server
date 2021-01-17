<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\CombinationApi\Client\Request\Job\UpdateRequest;
use FactorioItemBrowser\CombinationApi\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Handler\Job\UpdateHandler;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\JobService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The PHPUnit test of the UpdateHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Handler\Job\UpdateHandler
 */
class UpdateHandlerTest extends TestCase
{
    /**
     * @throws ServerException
     */
    public function testHandle(): void
    {
        $jobId = 'abc';
        $status = 'def';
        $errorMessage = 'ghi';
        $job = $this->createMock(Job::class);
        $detailsResponse = $this->createMock(DetailsResponse::class);

        $clientRequest = new UpdateRequest();
        $clientRequest->status = $status;
        $clientRequest->errorMessage = $errorMessage;

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo('job-id'))
                ->willReturn($jobId);
        $request->expects($this->once())
                ->method('getParsedBody')
                ->willReturn($clientRequest);

        $jobService = $this->createMock(JobService::class);
        $jobService->expects($this->once())
                   ->method('getJobFromRequestValue')
                   ->with($this->identicalTo($jobId))
                   ->willReturn($job);
        $jobService->expects($this->once())
                   ->method('changeJob')
                   ->with($this->identicalTo($job), $this->identicalTo($status), $this->identicalTo($errorMessage));

        $mapperManager = $this->createMock(MapperManagerInterface::class);
        $mapperManager->expects($this->once())
                      ->method('map')
                      ->with($this->identicalTo($job), $this->isInstanceOf(DetailsResponse::class))
                      ->willReturn($detailsResponse);

        $instance = new UpdateHandler($jobService, $mapperManager);
        $result = $instance->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
        /** @var ClientResponse $result */
        $this->assertSame($detailsResponse, $result->getPayload());
    }
}

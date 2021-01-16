<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\CombinationApi\Client\Response\Job\ListResponse;
use FactorioItemBrowser\CombinationApi\Client\Transfer\Job as ClientJob;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job as DatabaseJob;
use FactorioItemBrowser\CombinationApi\Server\Handler\Job\ListHandler;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\JobService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The PHPUnit test of the ListHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Handler\Job\ListHandler
 */
class ListHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $queryParams = ['abc' => 'def'];

        $databaseJob1 = $this->createMock(DatabaseJob::class);
        $databaseJob2 = $this->createMock(DatabaseJob::class);
        $clientJob1 = $this->createMock(ClientJob::class);
        $clientJob2 = $this->createMock(ClientJob::class);

        $expectedPayload = new ListResponse();
        $expectedPayload->jobs = [$clientJob1, $clientJob2];

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->any())
                ->method('getQueryParams')
                ->willReturn($queryParams);

        $jobService = $this->createMock(JobService::class);
        $jobService->expects($this->once())
                   ->method('getJobsFromQueryParams')
                   ->with($this->identicalTo($queryParams))
                   ->willReturn([$databaseJob1, $databaseJob2]);

        $mapperManager = $this->createMock(MapperManagerInterface::class);
        $mapperManager->expects($this->exactly(2))
                      ->method('map')
                      ->withConsecutive(
                          [$this->identicalTo($databaseJob1), $this->isInstanceOf(ClientJob::class)],
                          [$this->identicalTo($databaseJob2), $this->isInstanceOf(ClientJob::class)],
                      )
                      ->willReturnOnConsecutiveCalls(
                          $clientJob1,
                          $clientJob2
                      );

        $instance = new ListHandler($jobService, $mapperManager);
        $result = $instance->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
        /** @var ClientResponse $result */
        $this->assertEquals($expectedPayload, $result->getPayload());
    }
}

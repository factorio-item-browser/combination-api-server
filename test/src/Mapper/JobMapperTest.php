<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Mapper;

use BluePsyduck\MapperManager\MapperManagerInterface;
use DateTimeImmutable;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use FactorioItemBrowser\CombinationApi\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\CombinationApi\Client\Transfer\Job as ClientJob;
use FactorioItemBrowser\CombinationApi\Client\Transfer\JobChange as ClientJobChange;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job as DatabaseJob;
use FactorioItemBrowser\CombinationApi\Server\Entity\JobChange as DatabaseJobChange;
use FactorioItemBrowser\CombinationApi\Server\Mapper\JobMapper;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

/**
 * The PHPUnit test of the JobMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Mapper\JobMapper
 */
class JobMapperTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function provideSupports(): array
    {
        return [
            [new DatabaseJob(), new ClientJob(), true],
            [new DatabaseJob(), new DetailsResponse(), true],

            [new DatabaseJob(), new stdClass(), false],
            [new stdClass(), new ClientJob(), false],
        ];
    }

    /**
     * @param object $source
     * @param object $destination
     * @param bool $expectedResult
     * @dataProvider provideSupports
     */
    public function testSupports(object $source, object $destination, bool $expectedResult): void
    {
        $instance = new JobMapper();
        $result = $instance->supports($source, $destination);

        $this->assertSame($expectedResult, $result);
    }

    public function testMap(): void
    {
        $combination = new Combination();
        $combination->setId(Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76'));

        $databaseChange1 = new DatabaseJobChange();
        $databaseChange1->setStatus(JobStatus::QUEUED)
                        ->setTimestamp(new DateTimeImmutable('2038-01-19 03:14:07'));

        $databaseChange2 = new DatabaseJobChange();
        $databaseChange2->setStatus(JobStatus::ERROR);

        $clientJobChange1 = $this->createMock(ClientJobChange::class);
        $clientJobChange2 = $this->createMock(ClientJobChange::class);

        $source = new DatabaseJob();
        $source->setId(Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'))
               ->setCombination($combination)
               ->setPriority('abc')
               ->setStatus('def')
               ->setErrorMessage('ghi');
        $source->getChanges()->add($databaseChange1);
        $source->getChanges()->add($databaseChange2);

        $expectedDestination = new ClientJob();
        $expectedDestination->id = '01234567-89ab-cdef-0123-456789abcdef';
        $expectedDestination->combinationId = '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76';
        $expectedDestination->priority = 'abc';
        $expectedDestination->status = 'def';
        $expectedDestination->errorMessage = 'ghi';
        $expectedDestination->creationTime = new DateTimeImmutable('2038-01-19 03:14:07');
        $expectedDestination->changes = [
            $clientJobChange1,
            $clientJobChange2,
        ];

        $destination = new ClientJob();

        $mapperManager = $this->createMock(MapperManagerInterface::class);
        $mapperManager->expects($this->exactly(2))
                      ->method('map')
                      ->withConsecutive(
                          [$this->identicalTo($databaseChange1), $this->isInstanceOf(ClientJobChange::class)],
                          [$this->identicalTo($databaseChange2), $this->isInstanceOf(ClientJobChange::class)],
                      )
                      ->willReturnOnConsecutiveCalls(
                          $clientJobChange1,
                          $clientJobChange2,
                      );

        $instance = new JobMapper();
        $instance->setMapperManager($mapperManager);
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }
}

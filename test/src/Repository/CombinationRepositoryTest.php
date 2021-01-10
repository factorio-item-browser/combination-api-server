<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use FactorioItemBrowser\CombinationApi\Server\Repository\CombinationRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the CombinationRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Repository\CombinationRepository
 */
class CombinationRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::findById
     */
    public function testFindById(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('setParameter')
              ->with(
                  $this->identicalTo('combinationId'),
                  $this->identicalTo($combinationId),
                  $this->identicalTo(UuidBinaryType::NAME),
              );
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willReturn($combination);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('createQuery')
                      ->with($this->isType('string'))
                      ->willReturn($query);

        $instance = new CombinationRepository($entityManager);
        $result = $instance->findById($combinationId);

        $this->assertSame($combination, $result);
    }

    /**
     * @covers ::findById
     */
    public function testFindByIdWithException(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('setParameter')
              ->with(
                  $this->identicalTo('combinationId'),
                  $this->identicalTo($combinationId),
                  $this->identicalTo(UuidBinaryType::NAME),
              );
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willThrowException($this->createMock(NonUniqueResultException::class));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('createQuery')
                      ->with($this->isType('string'))
                      ->willReturn($query);

        $instance = new CombinationRepository($entityManager);
        $result = $instance->findById($combinationId);

        $this->assertNull($result);
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);
        $mod1 = $this->createMock(Mod::class);
        $mod2 = $this->createMock(Mod::class);

        $expectedCombination = new Combination();
        $expectedCombination->setId($combinationId);
        $expectedCombination->getMods()->add($mod1);
        $expectedCombination->getMods()->add($mod2);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('persist')
                      ->with($this->equalTo($expectedCombination));
        $entityManager->expects($this->once())
                      ->method('flush');

        $instance = new CombinationRepository($entityManager);
        $result = $instance->create($combinationId, [$mod1, $mod2]);

        $this->assertEquals($expectedCombination, $result);
    }
}

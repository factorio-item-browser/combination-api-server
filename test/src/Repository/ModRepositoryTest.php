<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use FactorioItemBrowser\CombinationApi\Server\Repository\ModRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the ModRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Repository\ModRepository
 */
class ModRepositoryTest extends TestCase
{
    public function testFindByNames(): void
    {
        $modNames = ['abc', 'def'];
        $queryResult = [
            $this->createMock(Mod::class),
            $this->createMock(Mod::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('setParameter')
              ->with($this->identicalTo('modNames'), $this->identicalTo($modNames))
              ->willReturnSelf();
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('createQuery')
                      ->with($this->isType('string'))
                      ->willReturn($query);

        $instance = new ModRepository($entityManager);
        $result = $instance->findByNames($modNames);

        $this->assertSame($queryResult, $result);
    }

    public function testCreate(): void
    {
        $modName = 'abc';

        $expectedMod = new Mod();
        $expectedMod->setId(Uuid::fromString('900150983cd24fb0d6963f7d28e17f72'))
                    ->setName('abc');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('persist')
                      ->with($this->equalTo($expectedMod));
        $entityManager->expects($this->once())
                      ->method('flush');

        $instance = new ModRepository($entityManager);
        $result = $instance->create($modName);

        $this->assertEquals($expectedMod, $result);
    }
}

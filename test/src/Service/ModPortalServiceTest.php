<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Service;

use BluePsyduck\FactorioModPortalClient\Client\ClientInterface;
use BluePsyduck\FactorioModPortalClient\Entity\Mod;
use BluePsyduck\FactorioModPortalClient\Entity\Release;
use BluePsyduck\FactorioModPortalClient\Entity\Version;
use BluePsyduck\FactorioModPortalClient\Exception\ClientException;
use BluePsyduck\FactorioModPortalClient\Request\FullModRequest;
use FactorioItemBrowser\CombinationApi\Server\Service\ModPortalService;
use GuzzleHttp\Promise\FulfilledPromise;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ModPortalService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Service\ModPortalService
 */
class ModPortalServiceTest extends TestCase
{
    public function testRequestMods(): void
    {
        $modNames = ['abc', 'def', 'ghi'];
        $expectedRequest1 = new FullModRequest();
        $expectedRequest1->setName('abc');
        $expectedRequest2 = new FullModRequest();
        $expectedRequest2->setName('def');
        $expectedRequest3 = new FullModRequest();
        $expectedRequest3->setName('ghi');
        $mod1 = new Mod();
        $mod1->setName('abc');
        $mod2 = new Mod();
        $mod2->setName('def');
        $promise1 = new FulfilledPromise($mod1);
        $promise2 = new FulfilledPromise($mod2);
        $expectedResult = [
            'abc' => $mod1,
            'def' => $mod2,
        ];

        $modPortalClient = $this->createMock(ClientInterface::class);
        $modPortalClient->expects($this->exactly(3))
                        ->method('sendRequest')
                        ->withConsecutive(
                            [$this->equalTo($expectedRequest1)],
                            [$this->equalTo($expectedRequest2)],
                            [$this->equalTo($expectedRequest3)],
                        )
                        ->willReturnOnConsecutiveCalls(
                            $promise1,
                            $promise2,
                            $this->throwException($this->createMock(ClientException::class)),
                        );

        $instance = new ModPortalService($modPortalClient);
        $result = $instance->requestMods($modNames);

        $this->assertEquals($expectedResult, $result);
    }

    public function testSelectLatestReleases(): void
    {
        $release1 = new Release();
        $release1->getInfoJson()->setFactorioVersion(new Version('1.2.3'));
        $mod1 = new Mod();
        $mod1->setReleases([$release1]);

        $release2 = new Release();
        $release2->getInfoJson()->setFactorioVersion(new Version('1.2.3'));
        $mod2 = new Mod();
        $mod2->setReleases([$release2]);

        $mod3 = new Mod();

        $mods = [
            'abc' => $mod1,
            'def' => $mod2,
            'ghi' => $mod3,
        ];
        $expectedResult = [
            'abc' => $release1,
            'def' => $release2,
        ];

        $instance = new ModPortalService($this->createMock(ClientInterface::class));
        $result = $instance->selectLatestReleases($mods, new Version('1.2.3'));

        $this->assertEquals($expectedResult, $result);
    }
}

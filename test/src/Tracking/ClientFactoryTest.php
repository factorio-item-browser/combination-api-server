<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Tracking;

use BluePsyduck\Ga4MeasurementProtocol\Client;
use BluePsyduck\Ga4MeasurementProtocol\ClientInterface;
use BluePsyduck\Ga4MeasurementProtocol\Config;
use BluePsyduck\Ga4MeasurementProtocol\Serializer\Serializer;
use FactorioItemBrowser\CombinationApi\Server\Constant\ConfigKey;
use FactorioItemBrowser\CombinationApi\Server\Tracking\ClientFactory;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ClientFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Tracking\ClientFactory
 */
class ClientFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function testInvoke(): void
    {
        $measurementId = 'abc';
        $apiSecret = 'def';

        $config = [
            ConfigKey::MAIN => [
                ConfigKey::TRACKING => [
                    ConfigKey::TRACKING_MEASUREMENT_ID => $measurementId,
                    ConfigKey::TRACKING_API_SECRET => $apiSecret,
                ],
            ],
        ];

        $expectedConfig = new Config();
        $expectedConfig->measurementId = $measurementId;
        $expectedConfig->apiSecret = $apiSecret;

        $expectedResult = new Client(
            new GuzzleClient(),
            new HttpFactory(),
            new HttpFactory(),
            new Serializer(),
            $expectedConfig,
        );

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
                  ->method('get')
                  ->with($this->identicalTo('config'))
                  ->willReturn($config);

        $factory = new ClientFactory();
        $result = $factory($container, ClientInterface::class);

        $this->assertEquals($expectedResult, $result);
    }
}

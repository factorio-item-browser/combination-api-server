<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Repository;

use FactorioItemBrowser\CombinationApi\Server\Constant\ConfigKey;
use FactorioItemBrowser\CombinationApi\Server\Entity\Agent;
use FactorioItemBrowser\CombinationApi\Server\Repository\AgentRepository;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the AgentRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Repository\AgentRepository
 */
class AgentRepositoryTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function provideFindByApiKey(): array
    {
        $config = [
            [
                ConfigKey::AGENT_NAME => 'abc',
                ConfigKey::AGENT_ACCESS_KEY => 'def',
                ConfigKey::AGENT_CAN_CREATE_JOBS => true,
                ConfigKey::AGENT_CAN_UPDATE_JOBS => true,
            ],
            [
                ConfigKey::AGENT_NAME => 'ghi',
                ConfigKey::AGENT_ACCESS_KEY => 'jkl',
                ConfigKey::AGENT_CAN_CREATE_JOBS => false,
                ConfigKey::AGENT_CAN_UPDATE_JOBS => false,
            ],
        ];

        $agent1 = new Agent();
        $agent1->setName('abc')
               ->setApiKey('def')
               ->setCanCreateJobs(true)
               ->setCanUpdateJobs(true);

        $agent2 = new Agent();
        $agent2->setName('ghi')
               ->setApiKey('jkl')
               ->setCanCreateJobs(false)
               ->setCanUpdateJobs(false);

        return [
            [$config, 'def', $agent1],
            [$config, 'jkl', $agent2],
            [$config, 'missing', null],
        ];
    }

    /**
     * @param array<mixed> $config
     * @param string $apiKey
     * @param Agent|null $expectedResult
     * @dataProvider provideFindByApiKey
     */
    public function testFindByApiKey(array $config, string $apiKey, ?Agent $expectedResult): void
    {
        $instance = new AgentRepository($config);
        $result = $instance->findByApiKey($apiKey);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetAndGetCurrentAgent(): void
    {
        $value = $this->createMock(Agent::class);
        $instance = new AgentRepository([]);

        $this->assertSame($instance, $instance->setCurrentAgent($value));
        $this->assertSame($value, $instance->getCurrentAgent());
    }
}

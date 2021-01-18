<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Entity;

use FactorioItemBrowser\CombinationApi\Server\Entity\Agent;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the Agent class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Entity\Agent
 */
class AgentTest extends TestCase
{
    public function testSetAndGetName(): void
    {
        $value = 'abc';
        $instance = new Agent();

        $this->assertSame($instance, $instance->setName($value));
        $this->assertSame($value, $instance->getName());
    }

    public function testSetAndGetApiKey(): void
    {
        $value = 'abc';
        $instance = new Agent();

        $this->assertSame($instance, $instance->setApiKey($value));
        $this->assertSame($value, $instance->getApiKey());
    }

    public function testSetAndGetCanCreateJobs(): void
    {
        $instance = new Agent();

        $this->assertSame($instance, $instance->setCanCreateJobs(true));
        $this->assertTrue($instance->getCanCreateJobs());
    }

    public function testSetAndGetCanUpdateJobs(): void
    {
        $instance = new Agent();

        $this->assertSame($instance, $instance->setCanUpdateJobs(true));
        $this->assertTrue($instance->getCanUpdateJobs());
    }
}

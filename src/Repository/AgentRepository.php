<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Repository;

use FactorioItemBrowser\CombinationApi\Server\Constant\ConfigKey;
use FactorioItemBrowser\CombinationApi\Server\Entity\Agent;

/**
 * The repository for the agents.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AgentRepository
{
    /** @var array<Agent> */
    private array $agents;
    private Agent $currentAgent;

    /**
     * @param array<mixed> $agents
     */
    public function __construct(array $agents)
    {
        $this->agents = array_map([$this, 'createAgent'], $agents);
    }

    /**
     * @param array<mixed> $agentConfig
     * @return Agent
     */
    private function createAgent(array $agentConfig): Agent
    {
        $agent = new Agent();
        $agent->setName($agentConfig[ConfigKey::AGENT_NAME])
              ->setApiKey($agentConfig[ConfigKey::AGENT_ACCESS_KEY])
              ->setCanCreateJobs($agentConfig[ConfigKey::AGENT_CAN_CREATE_JOBS] ?? false)
              ->setCanUpdateJobs($agentConfig[ConfigKey::AGENT_CAN_UPDATE_JOBS] ?? false);
        return $agent;
    }

    /**
     * Searches for the agent with the specified API key.
     * @param string $apiKey
     * @return Agent|null
     */
    public function findByApiKey(string $apiKey): ?Agent
    {
        foreach ($this->agents as $agent) {
            if ($agent->getApiKey() === $apiKey) {
                return $agent;
            }
        }
        return null;
    }

    /**
     * Sets the agent of the current request.
     * @param Agent $currentAgent
     * @return $this
     */
    public function setCurrentAgent(Agent $currentAgent): self
    {
        $this->currentAgent = $currentAgent;
        return $this;
    }

    /**
     * Returns the agent of the current request.
     * @return Agent
     */
    public function getCurrentAgent(): Agent
    {
        return $this->currentAgent;
    }
}

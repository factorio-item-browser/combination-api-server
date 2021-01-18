<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Entity;

/**
 * The class representing an agent of the API.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Agent
{
    private string $name = '';
    private string $apiKey = '';
    private bool $canCreateJobs = false;
    private bool $canUpdateJobs = false;

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setCanCreateJobs(bool $canCreateJobs): self
    {
        $this->canCreateJobs = $canCreateJobs;
        return $this;
    }

    public function getCanCreateJobs(): bool
    {
        return $this->canCreateJobs;
    }

    public function setCanUpdateJobs(bool $canUpdateJobs): self
    {
        $this->canUpdateJobs = $canUpdateJobs;
        return $this;
    }

    public function getCanUpdateJobs(): bool
    {
        return $this->canUpdateJobs;
    }
}

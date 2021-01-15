<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use Ramsey\Uuid\UuidInterface;

/**
 * The class representing an export job.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Job
{
    private UuidInterface $id;
    private Combination $combination;
    private string $priority;
    private string $status;
    private string $errorMessage;
    /** @var Collection<int, JobChange> */
    private Collection $changes;

    public function __construct()
    {
        $this->changes = new ArrayCollection();
    }

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setCombination(Combination $combination): self
    {
        $this->combination = $combination;
        return $this;
    }

    public function getCombination(): Combination
    {
        return $this->combination;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setErrorMessage(string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return Collection<int, JobChange>
     */
    public function getChanges(): Collection
    {
        return $this->changes;
    }

    public function getCreationTime(): DateTimeInterface
    {
        foreach ($this->changes as $change) {
            /* @var JobChange $change */
            if ($change->getStatus() === JobStatus::QUEUED) {
                return $change->getTimestamp();
            }
        }

        // Fallback if the job is missing initial status change, which should never happen.
        return new DateTimeImmutable();
    }
}

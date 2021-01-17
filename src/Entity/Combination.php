<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use Ramsey\Uuid\UuidInterface;

/**
 * The class representing a combination of mods.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Combination
{
    private UuidInterface $id;
    private ?DateTimeInterface $exportTime = null;

    /** @var Collection<int, Mod> */
    private Collection $mods;
    /** @var Collection<int, Job> */
    private Collection $jobs;

    public function __construct()
    {
        $this->mods = new ArrayCollection();
        $this->jobs = new ArrayCollection();
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

    public function setExportTime(?DateTimeInterface $exportTime): self
    {
        $this->exportTime = $exportTime;
        return $this;
    }

    public function getExportTime(): ?DateTimeInterface
    {
        return $this->exportTime;
    }

    /**
     * @return Collection<int, Mod>
     */
    public function getMods(): Collection
    {
        return $this->mods;
    }

    /**
     * @return array<string>
     */
    public function getModNames(): array
    {
        return array_map(fn(Mod $mod): string => $mod->getName(), $this->getMods()->toArray());
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    /**
     * Returns the currently not finished export job of the combination, if it already exists.
     * @return Job|null
     */
    public function getUnfinishedJob(): ?Job
    {
        foreach ($this->jobs as $job) {
            /* @var Job $job */
            if ($job->getStatus() !== JobStatus::DONE && $job->getStatus() !== JobStatus::ERROR) {
                return $job;
            }
        }

        return null;
    }
}

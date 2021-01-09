<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @return Collection<int, Mod>
     */
    public function getMods(): Collection
    {
        return $this->mods;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The class representing a combination of mods.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collate' => 'utf8mb4_bin',
    'comment' => 'The table holding the combinations.',
])]
class Combination
{
    #[Id]
    #[Column(type: UuidBinaryType::NAME, options: ['comment' => 'The id of the combination.'])]
    private UuidInterface $id;

    #[Column(
        type: Types::DATETIME_MUTABLE,
        nullable: true,
        options: ['comment' => 'The time when the combination was last exported.'],
    )]
    private ?DateTimeInterface $exportTime = null;

    /** @var Collection<int, Mod> */
    #[ManyToMany(targetEntity: Mod::class)]
    #[JoinTable(name: 'CombinationXMod')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'modId', nullable: false)]
    #[OrderBy(['name' => 'ASC'])]
    private Collection $mods;

    /** @var Collection<int, Job> */
    #[OneToMany(mappedBy: 'combination', targetEntity: Job::class)]
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

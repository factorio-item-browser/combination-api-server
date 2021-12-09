<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\CombinationApi\Server\Doctrine\Type\JobPriorityType;
use FactorioItemBrowser\CombinationApi\Server\Doctrine\Type\JobStatusType;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * The class representing an export job.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collate' => 'utf8mb4_bin',
    'comment' => 'The table holding the export jobs.',
])]
class Job
{
    #[Id]
    #[Column(type: UuidBinaryType::NAME, options: ['comment' => 'The id of the job.'])]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ManyToOne(targetEntity: Combination::class, inversedBy: 'jobs')]
    #[JoinColumn(name: 'combinationId')]
    private Combination $combination;

    #[Column(type: JobPriorityType::NAME, options: ['comment' => 'The priority of the export job.'])]
    private string $priority;

    #[Column(type: JobStatusType::NAME, options: ['comment' => 'The current status of the export job.'])]
    private string $status;

    #[Column(type: Types::DATETIME_MUTABLE, options: ['comment' => 'The creation time of the job.'])]
    private DateTimeInterface $creationTime;

    #[Column(type: Types::TEXT, options: ['comment' => 'The error message in case the job failed.'])]
    private string $errorMessage;

    /** @var Collection<int, JobChange> */
    #[OneToMany(mappedBy: 'job', targetEntity: JobChange::class)]
    #[OrderBy(['timestamp' => 'ASC'])]
    private Collection $changes;

    public function __construct()
    {
        $this->creationTime = new DateTimeImmutable();
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

    public function setCreationTime(DateTimeInterface $creationTime): self
    {
        $this->creationTime = $creationTime;
        return $this;
    }

    public function getCreationTime(): DateTimeInterface
    {
        return $this->creationTime;
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
}

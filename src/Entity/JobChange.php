<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\CombinationApi\Server\Doctrine\Type\JobStatusType;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * The class representing a change of a job.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collate' => 'utf8mb4_bin',
    'comment' => 'The table holding the changes of the export jobs.',
])]
class JobChange
{
    #[Id]
    #[Column(type: UuidBinaryType::NAME, options: ['comment' => 'The internal id of the job change.'])]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ManyToOne(targetEntity: Job::class, inversedBy: 'changes')]
    #[JoinColumn(name: 'jobId')]
    private Job $job;

    #[Column(length: 255, options: ['comment' => 'The initiator of the change.'])]
    private string $initiator;

    #[Column(type: JobStatusType::NAME, options: ['comment' => 'The new status of the export job.'])]
    private string $status;

    #[Column(type: Types::DATETIME_MUTABLE, options: ['comment' => 'The time of the change.'])]
    private DateTimeInterface $timestamp;

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setJob(Job $job): self
    {
        $this->job = $job;
        return $this;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setInitiator(string $initiator): self
    {
        $this->initiator = $initiator;
        return $this;
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

    public function getInitiator(): string
    {
        return $this->initiator;
    }

    public function setTimestamp(DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Doctrine\Type;

use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;

/**
 * The enum type for the job status.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobStatusType extends AbstractEnumType
{
    public const NAME = 'job_status';
    public const VALUES = [
        JobStatus::QUEUED,
        JobStatus::DOWNLOADING,
        JobStatus::PROCESSING,
        JobStatus::UPLOADING,
        JobStatus::UPLOADED,
        JobStatus::IMPORTING,
        JobStatus::DONE,
        JobStatus::ERROR,
    ];
}

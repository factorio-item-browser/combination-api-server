<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Doctrine\Type;

use FactorioItemBrowser\CombinationApi\Client\Constant\JobPriority;

/**
 * The enum type for the job priorities.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobPriorityType extends AbstractEnumType
{
    public const NAME = 'job_priority';
    public const VALUES = [ // Order implies priority, highest priority first.
        JobPriority::ADMIN,
        JobPriority::USER,
        JobPriority::AUTO_UPDATE,
    ];
}

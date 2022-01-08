<?php

/**
 * The configuration of the Doctrine integration.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Ramsey\Uuid\Doctrine\UuidBinaryType;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'doctrine_mapping_types' => [
                    UuidBinaryType::NAME => Types::BINARY,
                    'enum' => 'string',
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => AttributeDriver::class,
                'cache' => 'array',
                'paths' => [
                    'src/Entity',
                ],
            ],
        ],
        'migrations' => [
            'orm_default' => [
                'table_storage' => [
                    'table_name' => '_Migrations',
                ],
                'migrations_paths' => [
                    'FactorioItemBrowser\CombinationApi\Server\Migrations' => 'data/migrations'
                ],
            ],
        ],
        'types' => [
            Doctrine\Type\JobPriorityType::NAME => Doctrine\Type\JobPriorityType::class,
            Doctrine\Type\JobStatusType::NAME => Doctrine\Type\JobStatusType::class,
            UuidBinaryType::NAME => UuidBinaryType::class,
        ],
    ],
];

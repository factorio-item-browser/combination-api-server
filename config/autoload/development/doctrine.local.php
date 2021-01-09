<?php

/**
 * The configuration of doctrine.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use Doctrine\DBAL\Driver\PDO\MySQL\Driver as PDOMySqlDriver;
use PDO;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => [
                    'host'     => 'fib-mysql',
                    'port'     => '3306',
                    'user'     => 'combination-api',
                    'password' => 'combination-api',
                    'dbname'   => 'combination-api',
                    'driverOptions' => [
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    ],
                ],
            ],
        ],
    ],
];

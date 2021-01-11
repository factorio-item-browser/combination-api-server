<?php

/**
 * The configuration of the project dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use BluePsyduck\LaminasAutoWireFactory\AutoWireFactory;
use ContainerInteropDoctrine\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;
use Mezzio\Middleware\ErrorResponseGenerator;
use Roave\PsrContainerDoctrine\MigrationsConfigurationFactory;

use function BluePsyduck\LaminasAutoWireFactory\readConfig;

return [
    'dependencies' => [
        'aliases' => [
            ErrorResponseGenerator::class => Response\ErrorResponseGenerator::class,
        ],
        'factories' => [
            Handler\Combination\StatusHandler::class => AutoWireFactory::class,
            Handler\NotFoundHandler::class => AutoWireFactory::class,

            Helper\CombinationIdCalculator::class => AutoWireFactory::class,

            Mapper\StatusResponseMapper::class => AutoWireFactory::class,

            Middleware\MetaMiddleware::class => AutoWireFactory::class,
            Middleware\ResponseSerializerMiddleware::class => AutoWireFactory::class,

            Repository\CombinationRepository::class => AutoWireFactory::class,
            Repository\ModRepository::class => AutoWireFactory::class,

            Response\ErrorResponseGenerator::class => AutoWireFactory::class,

            Service\CombinationService::class => AutoWireFactory::class,
            Service\ModService::class => AutoWireFactory::class,

            // 3rd-party dependencies
            EntityManagerInterface::class => EntityManagerFactory::class,
            'doctrine.migrations.orm_default' => MigrationsConfigurationFactory::class,

            // Auto-wire helpers
            'bool $debug' => readConfig('debug'),
            'string $version' => readConfig('version'),
        ],
    ],
];

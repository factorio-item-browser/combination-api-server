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
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use FactorioItemBrowser\CombinationApi\Server\Constant\ConfigKey;
use Mezzio\Middleware\ErrorResponseGenerator;
use Roave\PsrContainerDoctrine\EntityManagerFactory;
use Roave\PsrContainerDoctrine\Migrations\ConfigurationLoaderFactory;
use Roave\PsrContainerDoctrine\Migrations\DependencyFactoryFactory;

use function BluePsyduck\LaminasAutoWireFactory\readConfig;

return [
    'dependencies' => [
        'aliases' => [
            ErrorResponseGenerator::class => Response\ErrorResponseGenerator::class,
        ],
        'factories' => [
            Handler\Combination\StatusHandler::class => AutoWireFactory::class,
            Handler\Combination\ValidateHandler::class => AutoWireFactory::class,
            Handler\Job\CreateHandler::class => AutoWireFactory::class,
            Handler\Job\DetailsHandler::class => AutoWireFactory::class,
            Handler\Job\ListHandler::class => AutoWireFactory::class,
            Handler\Job\UpdateHandler::class => AutoWireFactory::class,
            Handler\NotFoundHandler::class => AutoWireFactory::class,

            Helper\CombinationIdCalculator::class => AutoWireFactory::class,
            Helper\QueuePositionHelper::class => AutoWireFactory::class,

            Mapper\CombinationMapper::class => AutoWireFactory::class,
            Mapper\JobChangeMapper::class => AutoWireFactory::class,
            Mapper\JobMapper::class => AutoWireFactory::class,

            Middleware\AgentMiddleware::class => AutoWireFactory::class,
            Middleware\MetaMiddleware::class => AutoWireFactory::class,
            Middleware\RequestDeserializerMiddleware::class => AutoWireFactory::class,
            Middleware\ResponseSerializerMiddleware::class => AutoWireFactory::class,

            Repository\AgentRepository::class => AutoWireFactory::class,
            Repository\CombinationRepository::class => AutoWireFactory::class,
            Repository\JobRepository::class => AutoWireFactory::class,
            Repository\ModRepository::class => AutoWireFactory::class,

            Response\ErrorResponseGenerator::class => AutoWireFactory::class,

            Service\CombinationService::class => AutoWireFactory::class,
            Service\JobService::class => AutoWireFactory::class,
            Service\ModPortalService::class => AutoWireFactory::class,
            Service\ModService::class => AutoWireFactory::class,
            Service\ValidationService::class => AutoWireFactory::class,

            // 3rd-party dependencies
            ConfigurationLoader::class => ConfigurationLoaderFactory::class,
            DependencyFactory::class => DependencyFactoryFactory::class,
            EntityManagerInterface::class => EntityManagerFactory::class,

            // Auto-wire helpers
            'array $requestClassesByRoutes' => readConfig(ConfigKey::MAIN, ConfigKey::REQUEST_CLASSES_BY_ROUTES),
            'array $agents' => readConfig(ConfigKey::MAIN, ConfigKey::AGENTS),
            'bool $debug' => readConfig('debug'),
            'string $version' => readConfig('version'),
        ],
    ],
];

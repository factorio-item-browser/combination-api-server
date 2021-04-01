<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Service;

use BluePsyduck\FactorioModPortalClient\Entity\Dependency;
use BluePsyduck\FactorioModPortalClient\Entity\Mod;
use BluePsyduck\FactorioModPortalClient\Entity\Release;
use BluePsyduck\FactorioModPortalClient\Entity\Version;
use FactorioItemBrowser\CombinationApi\Client\Constant\ValidationProblemType;
use FactorioItemBrowser\CombinationApi\Client\Transfer\ValidatedMod;
use FactorioItemBrowser\CombinationApi\Client\Transfer\ValidationProblem;
use FactorioItemBrowser\CombinationApi\Server\Service\ModPortalService;
use FactorioItemBrowser\CombinationApi\Server\Service\ValidationService;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ValidationService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Service\ValidationService
 */
class ValidationServiceTest extends TestCase
{
    public function testValidate(): void
    {
        $createProblem = function (string $type, string $dependency): ValidationProblem {
            $problem = new ValidationProblem();
            $problem->type = $type;
            $problem->dependency = $dependency;
            return $problem;
        };

        $baseMod = new Mod();
        $validatedBaseMod = new ValidatedMod();
        $validatedBaseMod->name = 'base';
        $validatedBaseMod->version = '1.2.3';

        $dummyMod1 = new Mod();
        $dummyRelease1 = new Release();
        $dummyRelease1->setVersion(new Version('1.0.0'));
        $validatedDummyMod1 = new ValidatedMod();
        $validatedDummyMod1->name = 'dummyMod1';
        $validatedDummyMod1->version = '1.0.0';

        $dummyMod2 = new Mod();
        $dummyRelease2 = new Release();
        $dummyRelease2->setVersion(new Version('2.0.0'));
        $validatedDummyMod2 = new ValidatedMod();
        $validatedDummyMod2->name = 'dummyMod2';
        $validatedDummyMod2->version = '2.0.0';

        $validatedMissingMod = new ValidatedMod();
        $validatedMissingMod->name = 'missingMod';
        $validatedMissingMod->problems = [$createProblem(ValidationProblemType::UNKNOWN_MOD, '')];

        $missingReleaseMod = new Mod();
        $validatedMissingReleaseMod = new ValidatedMod();
        $validatedMissingReleaseMod->name = 'missingReleaseMod';
        $validatedMissingReleaseMod->problems = [$createProblem(ValidationProblemType::NO_RELEASE, '')];

        $validMod = new Mod();
        $validRelease = new Release();
        $validRelease->setVersion(new Version('3.4.5'));
        $validRelease->getInfoJson()->setDependencies([
            new Dependency('base'),
            new Dependency('dummyMod1'),
            new Dependency('dummyMod2 >= 1.5'),
            new Dependency('? notExistingMod'),
            new Dependency('! notExistingMod2'),
        ]);
        $validatedValidMod = new ValidatedMod();
        $validatedValidMod->name = 'validMod';
        $validatedValidMod->version = '3.4.5';

        $missingDependency1Mod = new Mod();
        $missingDependency1Release = new Release();
        $missingDependency1Release->setVersion(new Version('4.5.6'));
        $missingDependency1Release->getInfoJson()->setDependencies([
            new Dependency('notExistingMod'),
            new Dependency('dummyMod > 2.0'),
        ]);
        $validatedMissingDependency1Mod = new ValidatedMod();
        $validatedMissingDependency1Mod->name = 'missingDependency1Mod';
        $validatedMissingDependency1Mod->version = '4.5.6';
        $validatedMissingDependency1Mod->problems = [
            $createProblem(ValidationProblemType::MISSING_DEPENDENCY, 'notExistingMod'),
            $createProblem(ValidationProblemType::MISSING_DEPENDENCY, 'dummyMod > 2.0.0'),
        ];

        $missingDependency2Mod = new Mod();
        $missingDependency2Release = new Release();
        $missingDependency2Release->setVersion(new Version('4.5.6'));
        $missingDependency2Release->getInfoJson()->setDependencies([
            new Dependency('notExistingMod'),
            new Dependency('~ dummyMod > 2.0'),
        ]);
        $validatedMissingDependency2Mod = new ValidatedMod();
        $validatedMissingDependency2Mod->name = 'missingDependency2Mod';
        $validatedMissingDependency2Mod->version = '4.5.6';
        $validatedMissingDependency2Mod->problems = [
            $createProblem(ValidationProblemType::MISSING_DEPENDENCY, 'notExistingMod'),
            $createProblem(ValidationProblemType::MISSING_DEPENDENCY, '~ dummyMod > 2.0.0'),
        ];

        $conflictedMod = new Mod();
        $conflictedRelease = new Release();
        $conflictedRelease->setVersion(new Version('5.6.7'));
        $conflictedRelease->getInfoJson()->setDependencies([
            new Dependency('! dummyMod1'),
            new Dependency('! dummyMod2')
        ]);
        $validatedConflictedMod = new ValidatedMod();
        $validatedConflictedMod->name = 'conflictedMod';
        $validatedConflictedMod->version = '5.6.7';
        $validatedConflictedMod->problems = [
            $createProblem(ValidationProblemType::CONFLICT, '! dummyMod1'),
            $createProblem(ValidationProblemType::CONFLICT, '! dummyMod2'),
        ];

        $factorioVersion = new Version('1.2.3');
        $modNames = [
            'base',
            'dummyMod1',
            'dummyMod2',
            'missingMod',
            'missingReleaseMod',
            'validMod',
            'missingDependency1Mod',
            'missingDependency2Mod',
            'conflictedMod',
        ];
        $mods = [
            'base' => $baseMod,
            'dummyMod1' => $dummyMod1,
            'dummyMod2' => $dummyMod2,
            'missingReleaseMod' => $missingReleaseMod,
            'validMod' => $validMod,
            'missingDependency1Mod' => $missingDependency1Mod,
            'missingDependency2Mod' => $missingDependency2Mod,
            'conflictedMod' => $conflictedMod,
        ];
        $releases = [
            'dummyMod1' => $dummyRelease1,
            'dummyMod2' => $dummyRelease2,
            'validMod' => $validRelease,
            'missingDependency1Mod' => $missingDependency1Release,
            'missingDependency2Mod' => $missingDependency2Release,
            'conflictedMod' => $conflictedRelease,
        ];
        $expectedResult = [
            'base' => $validatedBaseMod,
            'dummyMod1' => $validatedDummyMod1,
            'dummyMod2' => $validatedDummyMod2,
            'missingMod' => $validatedMissingMod,
            'missingReleaseMod' => $validatedMissingReleaseMod,
            'validMod' => $validatedValidMod,
            'missingDependency1Mod' => $validatedMissingDependency1Mod,
            'missingDependency2Mod' => $validatedMissingDependency2Mod,
            'conflictedMod' => $validatedConflictedMod,
        ];

        $modPortalService = $this->createMock(ModPortalService::class);
        $modPortalService->expects($this->once())
                         ->method('requestMods')
                         ->with($this->identicalTo($modNames))
                         ->willReturn($mods);
        $modPortalService->expects($this->once())
                         ->method('selectLatestReleases')
                         ->with($this->identicalTo($mods), $this->identicalTo($factorioVersion))
                         ->willReturn($releases);

        $instance = new ValidationService($modPortalService);
        $result = $instance->validate($modNames, $factorioVersion);

        $this->assertEquals($expectedResult, $result);
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use BluePsyduck\FactorioModPortalClient\Constant\DependencyType;
use BluePsyduck\FactorioModPortalClient\Entity\Dependency;
use BluePsyduck\FactorioModPortalClient\Entity\Release;
use BluePsyduck\FactorioModPortalClient\Entity\Version;
use FactorioItemBrowser\CombinationApi\Client\Constant\ValidationProblemType;
use FactorioItemBrowser\CombinationApi\Client\Transfer\ValidatedMod;
use FactorioItemBrowser\CombinationApi\Client\Transfer\ValidationProblem;
use FactorioItemBrowser\Common\Constant\Constant;

/**
 * The service for validating a combination of mods.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ValidationService
{
    private ModPortalService $modPortalService;

    public function __construct(ModPortalService $modPortalService)
    {
        $this->modPortalService = $modPortalService;
    }

    /**
     * Validates the mod names regarding whether they are compatible to each other using their latest releases.
     * @param array<string> $modNames
     * @param Version $factorioVersion
     * @return array<string, ValidatedMod>
     */
    public function validate(array $modNames, Version $factorioVersion): array
    {
        $mods = $this->modPortalService->requestMods($modNames);
        $releases = $this->modPortalService->selectLatestReleases($mods, $factorioVersion);

        $validatedMods = [];
        foreach ($modNames as $modName) {
            $validatedMod = new ValidatedMod();
            $validatedMod->name = $modName;
            $validatedMods[$modName] = $validatedMod;

            $mod = $mods[$modName] ?? null;
            $release = $releases[$modName] ?? null;

            if ($modName === Constant::MOD_NAME_BASE) {
                $validatedMod->version = (string) $factorioVersion;
                continue;
            }

            if ($mod === null) {
                $validatedMod->problems[] = $this->createProblem(ValidationProblemType::UNKNOWN_MOD);
                continue;
            }

            if ($release === null) {
                $validatedMod->problems[] = $this->createProblem(ValidationProblemType::NO_RELEASE);
                continue;
            }

            $validatedMod->version = (string) $release->getVersion();
            $validatedMod->problems = $this->validateRelease($release, $releases);
        }

        return $validatedMods;
    }

    /**
     * @param Release $release
     * @param array<string, Release> $allReleases
     * @return array<ValidationProblem>
     */
    private function validateRelease(Release $release, array $allReleases): array
    {
        $problems = [];
        foreach ($release->getInfoJson()->getDependencies() as $dependency) {
            if ($dependency->getMod() === Constant::MOD_NAME_BASE) {
                continue; // base mod already gets validated by selecting only a compatible release.
            }

            $dependentRelease = $allReleases[$dependency->getMod()] ?? null;
            switch ($dependency->getType()) {
                case DependencyType::MANDATORY:
                    if (
                        $dependentRelease === null
                        || !$dependency->isMatchedByVersion($dependentRelease->getVersion())
                    ) {
                        $problems[] = $this->createProblem(ValidationProblemType::MISSING_DEPENDENCY, $dependency);
                    }
                    break;

                case DependencyType::CONFLICT:
                    if ($dependentRelease !== null) {
                        $problems[] = $this->createProblem(ValidationProblemType::CONFLICT, $dependency);
                    }
                    break;
            }
        }
        return $problems;
    }

    private function createProblem(string $type, ?Dependency $dependency = null): ValidationProblem
    {
        $problem = new ValidationProblem();
        $problem->type = $type;
        $problem->dependency = (string) $dependency;
        return $problem;
    }
}

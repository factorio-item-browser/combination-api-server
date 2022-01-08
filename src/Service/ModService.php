<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use FactorioItemBrowser\CombinationApi\Server\Repository\ModRepository;

/**
 * The service handling the mods.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModService
{
    public function __construct(
        private readonly ModRepository $modRepository,
    ) {
    }

    /**
     * Returns the mods with the specified names. If they do not exist yet, they will be created.
     * @param array<string> $modNames
     * @return array<string, Mod>
     */
    public function getMods(array $modNames): array
    {
        $mods = [];
        foreach ($this->modRepository->findByNames($modNames) as $mod) {
            $mods[$mod->getName()] = $mod;
        }

        foreach ($modNames as $modName) {
            if (!isset($mods[$modName])) {
                $mods[$modName] = $this->modRepository->create($modName);
            }
        }

        return $mods;
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Repository;

use Doctrine\ORM\EntityManagerInterface;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use Ramsey\Uuid\Uuid;

/**
 * The repository for the mods.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Searches for the mods with the specified names.
     * @param array<string> $modNames
     * @return array<Mod>
     */
    public function findByNames(array $modNames): array
    {
        $entity = Mod::class;
        $query = $this->entityManager->createQuery("SELECT m FROM {$entity} m WHERE m.name IN (:modNames)");
        $query->setParameter('modNames', $modNames);
        return $query->getResult();
    }

    /**
     * Creates a new mod with the specified name and persists it into the database. The mod must not yet exist.
     * @param string $modName
     * @return Mod
     */
    public function create(string $modName): Mod
    {
        $mod = new Mod();
        $mod->setId(Uuid::fromString(hash('md5', $modName)))
            ->setName($modName);

        $this->entityManager->persist($mod);
        $this->entityManager->flush();

        return $mod;
    }
}

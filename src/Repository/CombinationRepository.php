<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository for the combinations.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CombinationRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Fetches the combination with the specified id.
     * @param UuidInterface $combinationId
     * @return Combination|null
     */
    public function findById(UuidInterface $combinationId): ?Combination
    {
        $entity = Combination::class;
        $query = $this->entityManager->createQuery(
            "SELECT c, m FROM {$entity} c LEFT JOIN c.mods m WHERE c.id = :combinationId"
        );
        $query->setParameter('combinationId', $combinationId, UuidBinaryType::NAME);
        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // Will never happen: We are searching for the primary key.
            return null;
        }
    }

    /**
     * Creates a new combination with the specified mods.
     * @param UuidInterface $id
     * @param array<Mod> $mods
     * @return Combination
     */
    public function create(UuidInterface $id, array $mods): Combination
    {
        $combination = new Combination();
        $combination->setId($id);
        foreach ($mods as $mod) {
            $combination->getMods()->add($mod);
        }

        $this->entityManager->persist($combination);
        $this->entityManager->flush();

        return $combination;
    }
}

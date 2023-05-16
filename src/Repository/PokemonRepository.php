<?php

namespace App\Repository;

use App\Entity\Pokemon;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Pokemon>
 *
 * @method Pokemon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pokemon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pokemon[]    findAll()
 * @method Pokemon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function save(Pokemon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Pokemon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUserPokemon(User|UserInterface|null $user): array
    {
        return $this->createQueryBuilder('p', 'p.id')
            ->select('p.id, p.number', 'up.shiny', 'up.normal', 'up.lucky', 'up.threeStars')
            ->join('p.userPokemon', 'up')
            ->andWhere('up.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.number', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getCountByGeneration(string $generation): int
    {
        /** @var int $result */
        $result = $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT(p.number))')
            ->where('p.generation = :generation')
            ->setParameter('generation', $generation)
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function countUnique(bool $shinyPokedex = false): int
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT(p.number))');

        if ($shinyPokedex) {
            $query->andWhere('p.isShiny = true');
        }

        /** @var int $result */
        $result = $query->getQuery()
            ->getSingleScalarResult();

        return $result;
    }
}

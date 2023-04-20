<?php

namespace App\Repository;

use App\Entity\CommandT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommandT>
 *
 * @method CommandT|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandT|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandT[]    findAll()
 * @method CommandT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandT::class);
    }

    public function save(CommandT $entity, bool $flush = false): bool
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $this->getEntityManager()->contains($entity);
    }

    public function remove(CommandT $entity, bool $flush = false): bool
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return !$this->getEntityManager()->contains($entity);
    }
}

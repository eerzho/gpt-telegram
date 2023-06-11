<?php

namespace App\Repository;

use App\Entity\ChatT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatT>
 *
 * @method ChatT|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatT|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatT[]    findAll()
 * @method ChatT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatT::class);
    }

    public function save(ChatT $entity, bool $flush = false): bool
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $this->getEntityManager()->contains($entity);
    }

    public function remove(ChatT $entity, bool $flush = false): bool
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return !$this->getEntityManager()->contains($entity);
    }

    public function findByTelegramId($id): ?ChatT
    {
        return $this->findOneBy(['telegram_id' => $id]);
    }

    /**
     * @return ChatT[]
     */
    public function getAll(): array
    {
        return $this->findAll();
    }
}

<?php

namespace App\Repository;

use App\Entity\MessageT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageT>
 *
 * @method MessageT|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageT|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageT[]    findAll()
 * @method MessageT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageT::class);
    }

    public function save(MessageT $entity, bool $flush = false): bool
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $this->getEntityManager()->contains($entity);
    }

    public function remove(MessageT $entity, bool $flush = false): bool
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return !$this->getEntityManager()->contains($entity);
    }

    public function removeAllByChatId(int $chatId): bool
    {
        return !($this->getEntityManager()
                ->createQuery('DELETE FROM App\Entity\MessageT m WHERE m.chat_t = :chatId')
                ->setParameter('chatId', $chatId)
                ->execute() === null);
    }
}

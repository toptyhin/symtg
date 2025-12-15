<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {

        $em = $this->getEntityManager();
        
        // Если EntityManager закрыт, получаем новый
        if (!$em->isOpen()) {
            $em = $this->registry->getManager();
        }
        
        $em->persist($entity);

        if ($flush) {
            $em->flush();
        }
    }


}

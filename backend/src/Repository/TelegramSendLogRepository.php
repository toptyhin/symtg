<?php

namespace App\Repository;

use App\Entity\TelegramSendLog;
use App\Entity\Shop;
use App\Enum\SendStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TelegramSendLog>
 */
class TelegramSendLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelegramSendLog::class);
    }

    public function getStatsForShop(Shop $shop, \DateTimeImmutable $from7d): array
    {
        $lastSent = $this->createQueryBuilder('l')
            ->select('MAX(l.sent_at) AS lastSentAt')
            ->where('l.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery()
            ->getSingleScalarResult();
    
        $counts = $this->createQueryBuilder('l')
            ->select('SUM(CASE WHEN l.status = :sent THEN 1 ELSE 0 END) AS sentCnt')
            ->addSelect('SUM(CASE WHEN l.status = :failed THEN 1 ELSE 0 END) AS failedCnt')
            ->where('l.shop = :shop')
            ->andWhere('l.sent_at >= :from')
            ->setParameter('shop', $shop)
            ->setParameter('from', $from7d)
            ->setParameter('sent', SendStatus::SENT)
            ->setParameter('failed', SendStatus::FAILED)
            ->getQuery()
            ->getSingleResult();
    
        return [
            'lastSentAt' => $lastSent ? new \DateTimeImmutable($lastSent) : null,
            'sent7d' => (int)($counts['sentCnt'] ?? 0),
            'failed7d' => (int)($counts['failedCnt'] ?? 0),
        ];
    }    

    public function save(TelegramSendLog $entity, bool $flush = false): void
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

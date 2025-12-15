<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\TelegramIntegration;
use App\Repository\ShopRepository;
use App\Repository\TelegramIntegrationRepository;
use App\Repository\TelegramSendLogRepository;



/**
 * @implements ProviderInterface<TelegramIntegration>
 */
final class TelegramStatusProvider implements ProviderInterface
{
    public function __construct(
        private ShopRepository $shopRepository,
        private TelegramIntegrationRepository $integrationRepository,
        private TelegramSendLogRepository $logRepository
    ) {}

    /**
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?TelegramIntegration
    {
        $shopId = $uriVariables['shopId'];
        $shop = $this->shopRepository->find($shopId);

        if (!$shop) {
            throw new \InvalidArgumentException('Shop not found');
        }

        $integration = $this->integrationRepository->findOneByShop($shop);
        
        if (!$integration) {
            return null;
        }

        $stats = $this->logRepository->getStatsForShop($shop, new \DateTimeImmutable('-7 days'));
        $integration
            ->setLastSentAt($stats['lastSentAt'])
            ->setSent7d($stats['sent7d'])
            ->setFailed7d($stats['failed7d']);


        return $integration;
    }
}
<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Shop;
use App\Entity\TelegramIntegration;
use App\Repository\ShopRepository;
use App\Repository\TelegramIntegrationRepository;
use Psr\Log\LoggerInterface;

/**
 * @implements ProcessorInterface<TelegramIntegration, TelegramIntegration>
 */
final class TelegramConnectProcessor implements ProcessorInterface
{
    public function __construct(
        private ShopRepository $shopRepository,
        private TelegramIntegrationRepository $integrationRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * @param array<string, mixed> $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): TelegramIntegration
    {
        // $data будет содержать десериализованные данные из тела запроса (bot_token, chat_id)

        $this->logger->debug('TelegramConnectProcessor: processing telegram connect', [
            'data' => $data,
            'uriVariables' => $uriVariables,
            'context' => $context,
            'operation' => $operation,
        ]);


        // $integration = new TelegramIntegration();

        /** debug */


        $shopId = $uriVariables['shopId'] ?? null;
        
        if (!$shopId) {
            throw new \InvalidArgumentException('shopId is missing from the URI.');
        }

        $shop = $this->shopRepository->find($shopId);


        if (!$shop) {
            throw new \InvalidArgumentException('Shop not found');
        }

        $integration = $this->integrationRepository->findOneByShop($shop);

        if (!$integration) {
            $integration = new TelegramIntegration();
            $integration->setShop($shop);
        }
        if (!$integration->getCreatedAt()) {
            $integration->setCreatedAt(new \DateTimeImmutable());
        }
        
        $integration->setUpdatedAt(new \DateTimeImmutable());


        // Обновляем данные из запроса
        $integration->setBotToken($data->getBotToken());
        $integration->setChatId($data->getChatId());
        if ($data->isEnabled() !== null) {
            $integration->setEnabled($data->isEnabled());
        } else {
            $integration->setEnabled(false);
        }

        $this->integrationRepository->save($integration, true);

        return $integration;
    }
}
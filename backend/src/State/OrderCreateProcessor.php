<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Order;
use App\Entity\Shop;
use App\Repository\OrderRepository;
use App\Repository\ShopRepository;
use App\Service\TelegramNotifierService;
use App\Service\TelegramSendLogService;
use App\Enum\SendStatus;

/**
 * @implements ProcessorInterface<Order, Order>
 */
final class OrderCreateProcessor implements ProcessorInterface
{
    

    public function __construct(
        private ShopRepository $shopRepository,
        private OrderRepository $orderRepository,
        private TelegramNotifierService $telegramNotifier,
        private TelegramSendLogService $telegramSendLogService
    ) {}

    /**
     * @param array<string, mixed> $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Order
    {

        $shopId = $uriVariables['shopId'] ?? null;
        
        if (!$shopId) {
            throw new \InvalidArgumentException('shopId is missing from the URI.');
        }

        $shop = $this->shopRepository->find($shopId);

        if (!$shop) {
            throw new \InvalidArgumentException('Shop not found');
        }
        
        $data->setShop($shop);
        if (!$data->getNumber()) {
            $data->setNumber(uniqid('order_' . $shopId . '_'));
        }

        if (!$data->getCreatedAt()) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }

        $this->orderRepository->save($data, true);

        // Добавляем в телеграм сообщение о новом заказе
        $telegramIntegration = $shop->getTelegramIntegration();
        if ($telegramIntegration) {
            $message = "Новый заказ: № " . $data->getNumber() . ", на сумму " . $data->getTotal() . " рублей. Клиент: " . $data->getCustomerName();
            try {
                $this->telegramNotifier->sendMessage(
                    $message,
                    $telegramIntegration->getBotToken(),
                    $telegramIntegration->getChatId()
                );
                
                // Успешная отправка сообщения в телеграм записываем в лог
                $this->telegramSendLogService->save(
                    $shop,
                    $data,
                    $message,
                    SendStatus::SENT
                );
            } catch (\Exception $e) {
                // Ошибка при отправке сообщения в телеграм записываем в лог
                $this->telegramSendLogService->save(
                    $shop,
                    $data,
                    $message,
                    SendStatus::FAILED,
                    $e->getMessage()
                );
            }
        }        

        return $data;
    }
}
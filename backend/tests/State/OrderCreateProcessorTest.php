<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Order;
use App\Entity\Shop;
use App\Entity\TelegramIntegration;
use App\Enum\SendStatus;
use App\Repository\OrderRepository;
use App\Repository\ShopRepository;
use App\Service\TelegramNotifierService;
use App\Service\TelegramSendLogService;
use App\State\OrderCreateProcessor;
use PHPUnit\Framework\TestCase;

class OrderCreateProcessorTest extends TestCase
{
    public function testProcessWithEnabledIntegrationSendsTelegramAndWritesSentLog(): void
    {
        // --- arrange ---

        $shopRepository = $this->createMock(ShopRepository::class);
        $orderRepository = $this->createMock(OrderRepository::class);
        $telegramNotifier = $this->createMock(TelegramNotifierService::class);
        $telegramSendLogService = $this->createMock(TelegramSendLogService::class);

        $processor = new OrderCreateProcessor(
            $shopRepository,
            $orderRepository,
            $telegramNotifier,
            $telegramSendLogService
        );

        // Магазин с включённой интеграцией
        $shop = new Shop();
        $shop->setId(1);
        $shop->setName('Test shop');

        $integration = new TelegramIntegration();
        $integration
            ->setShop($shop)
            ->setBotToken('BOT_TOKEN')
            ->setChatId('CHAT_ID')
            ->setEnabled(true); 

        $shop->setTelegramIntegration($integration);

        // Заказ
        $order = new Order();
        $order
            ->setNumber('123')
            ->setTotal('1000.00')
            ->setCustomerName('Иван Иванов');

        // shopRepository должен вернуть наш магазин по shopId
        $shopRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($shop);

        // orderRepository::save просто ожидаем, что будет вызван
        $orderRepository
            ->expects($this->once())
            ->method('save')
            ->with($order, true);

        // Ожидание вызова отправки в Telegram
        $telegramNotifier
            ->expects($this->once())
            ->method('sendMessage')
            ->with(
                $this->stringContains('Новый заказ'), // текст
                'BOT_TOKEN',
                'CHAT_ID'
            )
            ->willReturn(true);

        // Ожидание записи лога со статусом SENT
        $telegramSendLogService
            ->expects($this->once())
            ->method('save')
            ->with(
                $shop,
                $order,
                $this->stringContains('Новый заказ'),
                SendStatus::SENT,
                $this->anything(),   // error (null) – можно ослабить до anything()
                $this->anything()    // sentAt
            );

        $operation = $this->createMock(Operation::class);
        $uriVariables = ['shopId' => 1];

        // --- act ---
        $result = $processor->process($order, $operation, $uriVariables, []);

        // --- assert ---
        $this->assertSame($order, $result);
        $this->assertNotNull($order->getCreatedAt());
    }

    public function testRepeatedProcessDoesNotResendTelegramOrCreateDuplicateLog(): void
    {
        // --- arrange ---
        $shopRepository = $this->createMock(ShopRepository::class);
        $orderRepository = $this->createMock(OrderRepository::class);
        $telegramNotifier = $this->createMock(TelegramNotifierService::class);
        $telegramSendLogService = $this->createMock(TelegramSendLogService::class);

        $processor = new OrderCreateProcessor(
            $shopRepository,
            $orderRepository,
            $telegramNotifier,
            $telegramSendLogService
        );

        $shop = new Shop();
        $shop->setId(1);
        $shop->setName('Test shop');

        $integration = new TelegramIntegration();
        $integration
            ->setShop($shop)
            ->setBotToken('BOT_TOKEN')
            ->setChatId('CHAT_ID')
            ->setEnabled(true);

        $shop->setTelegramIntegration($integration);

        $order = new Order();
        $order
            ->setNumber('123')
            ->setTotal('1000.00')
            ->setCustomerName('Иван Иванов');

        // Предполагаем, что shop ищется каждый раз (2 вызова process)
        $shopRepository
            ->expects($this->exactly(2))
            ->method('find')
            ->with(1)
            ->willReturn($shop);

        // Сохранение заказа может вызываться дважды
        $orderRepository
            ->expects($this->exactly(2))
            ->method('save')
            ->with($order, true);

        // Идемпотентность: отправка сообщения в Telegram только один раз
        $telegramNotifier
            ->expects($this->once())
            ->method('sendMessage')
            ->with(
                $this->stringContains('Новый заказ'),
                'BOT_TOKEN',
                'CHAT_ID'
            )
            ->willReturn(true);

        // Идемпотентность: логируем только один раз (нет дублей telegram_send_log)

        $telegramSendLogService
        ->method('hasSuccessfulLog')
        ->willReturnOnConsecutiveCalls(false, true);

        $telegramSendLogService
            ->expects($this->once())
            ->method('save')
            ->with(
                $shop,
                $order,
                $this->stringContains('Новый заказ'),
                SendStatus::SENT,
                $this->anything(),
                $this->anything()
            );

        $operation = $this->createMock(Operation::class);
        $uriVariables = ['shopId' => 1];

        // --- act ---
        $result1 = $processor->process($order, $operation, $uriVariables, []);
        $result2 = $processor->process($order, $operation, $uriVariables, []);

        // --- assert ---
        $this->assertSame($order, $result1);
        $this->assertSame($order, $result2);
        $this->assertNotNull($order->getCreatedAt());
    }

    public function testProcessWritesFailedLogWhenTelegramClientFailsButOrderIsStillCreated(): void
    {
        // --- arrange ---
        $shopRepository = $this->createMock(ShopRepository::class);
        $orderRepository = $this->createMock(OrderRepository::class);
        $telegramNotifier = $this->createMock(TelegramNotifierService::class);
        $telegramSendLogService = $this->createMock(TelegramSendLogService::class);

        $processor = new OrderCreateProcessor(
            $shopRepository,
            $orderRepository,
            $telegramNotifier,
            $telegramSendLogService
        );

        $shop = new Shop();
        $shop->setId(1);
        $shop->setName('Test shop');

        $integration = new TelegramIntegration();
        $integration
            ->setShop($shop)
            ->setBotToken('BOT_TOKEN')
            ->setChatId('CHAT_ID')
            ->setEnabled(true);

        $shop->setTelegramIntegration($integration);

        $order = new Order();
        $order
            ->setNumber('123')
            ->setTotal('1000.00')
            ->setCustomerName('Иван Иванов');

        $shopRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($shop);

        $orderRepository
            ->expects($this->once())
            ->method('save')
            ->with($order, true);

        // Лога ещё нет → попытка отправки
        $telegramSendLogService
            ->method('hasSuccessfulLog')
            ->willReturn(false);

        // Эмулируем падение TelegramClient
        $telegramNotifier
            ->expects($this->once())
            ->method('sendMessage')
            ->willThrowException(new \RuntimeException('Telegram send error'));

        // Должен быть записан FAILED‑лог с текстом ошибки
        $telegramSendLogService
            ->expects($this->once())
            ->method('save')
            ->with(
                $shop,
                $order,
                $this->stringContains('Новый заказ'),
                SendStatus::FAILED,
                $this->stringContains('Telegram send error'),
                $this->anything()
            );

        $operation = $this->createMock(Operation::class);
        $uriVariables = ['shopId' => 1];

        // --- act ---
        $result = $processor->process($order, $operation, $uriVariables, []);

        // --- assert ---
        $this->assertSame($order, $result);
        $this->assertNotNull($order->getCreatedAt());
    }    

   
}
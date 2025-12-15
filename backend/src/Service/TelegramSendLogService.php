<?php

namespace App\Service;

use App\Entity\TelegramSendLog;
use App\Entity\Shop;
use App\Entity\Order;
use App\Enum\SendStatus;
use App\Repository\TelegramSendLogRepository;



class TelegramSendLogService
{
    public function __construct(
        private TelegramSendLogRepository $telegramSendLogRepository
    ) {
    }

    /**
     * Сохраняет лог отправки сообщения в Telegram.
     *
     * @param Shop $shop Магазин
     * @param Order $order Заказ
     * @param string $message Текст сообщения
     * @param SendStatus $status Статус отправки
     * @param string|null $error Сообщение об ошибке (опционально)
     * @param \DateTimeImmutable|null $sentAt Дата и время отправки (по умолчанию текущее время)
     * @return TelegramSendLog Сохраненная сущность лога
     */
    public function save(
        Shop $shop,
        Order $order,
        string $message,
        SendStatus $status,
        ?string $error = null,
        ?\DateTimeImmutable $sentAt = null
    ): TelegramSendLog {
        $log = new TelegramSendLog();
        $log->setShop($shop);
        $log->setOrder($order);
        $log->setMessage($message);
        $log->setStatus($status);
        $err_msg = $error ?? "";
        $log->setError($err_msg);

        
        $log->setSentAt($sentAt ?? new \DateTimeImmutable());
        
        $this->telegramSendLogRepository->save($log, true);

        return $log;
    }
}

<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramNotifierService
{

    public function __construct(
        HttpClientInterface $httpClient, 
    ) {
        $this->httpClient = $httpClient;
    }

    /**
     * Отправляет сообщение в Telegram.
     *
     * @param string $message Текст сообщения
     * @param string $botToken Токен бота
     * @param string $chatId ID чата
     * @return bool Возвращает true в случае успеха.
     * @throws \LogicException Если не указан ID чата и нет ID по умолчанию.
     * @throws \RuntimeException В случае ошибки API Telegram.
     */
    public function sendMessage(string $message, string $botToken, string $chatId): bool
    {
        if ($chatId === null) {
            throw new \LogicException('Chat ID is not specified and no default chat ID is configured.');
        }

        if ($botToken === null) {
            throw new \LogicException('Bot Token is not specified and no default bot token is configured.');
        }

        $url = sprintf('https://api.telegram.org/bot%s/sendMessage', $botToken);

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                $content = $response->getContent(false);
                throw new \RuntimeException("Failed to send Telegram message. Status: {$statusCode}, Content: {$content}");
            }
            return true;
        } catch (TransportExceptionInterface | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            throw new \RuntimeException('Failed to send Telegram message due to a network or HTTP error: ' . $e->getMessage(), 0, $e);
        } catch (DecodingExceptionInterface $e) {
            throw new \RuntimeException('Failed to decode Telegram API response: ' . $e->getMessage(), 0, $e);
        }
    }
}
<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TelegramBotApi
{
    private Telegram $api;

    public function __construct(string $botName)
    {
        $botApiKey  = getenv('TELEGRAM_BOT_TEST_API_KEY');

        try {
            $telegram = new Telegram($botApiKey, $botName);
            $telegram->handle();
            $this->api = $telegram;
        } catch (TelegramException $e) {
            Log::channel('connect_telegram_bot')->error($e->getMessage());
        }
    }

    /**
     * @throws TelegramException
     */
    public function sendMessage(string $chatId, string $message): ServerResponse
    {
        return Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'MarkdownV2',
        ]);
    }

    public function api(): Telegram
    {
        return $this->api;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Telegram\TelegramApi;

interface HistoryMessageInterface
{
    public function get(): array;
}

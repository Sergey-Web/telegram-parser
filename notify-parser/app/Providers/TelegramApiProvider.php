<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Telegram\TelegramApi\TelegramApi;
use App\Services\Telegram\TelegramApi\TelegramApiInterface;
use Illuminate\Support\ServiceProvider;

class TelegramApiProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TelegramApiInterface::class, function ($app) {
            return new TelegramApi();
        });
    }
}

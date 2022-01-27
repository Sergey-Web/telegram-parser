<?php

declare(strict_types=1);

namespace App\Services\Telegram\TelegramApi;

use danog\MadelineProto\API;

interface TelegramApiInterface
{
    function api(): API;
}

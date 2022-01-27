<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Telegram\TelegramApi\HistoryMessage;
use App\Services\Telegram\TelegramApi\TelegramApi;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class TelegramApiController extends Controller
{
    use ValidatesRequests;

    public function auth(TelegramApi $telegramApi)
    {
        return response()->json(['connection' => true]);
    }

    public function logout(TelegramApi $telegramApi)
    {
        $telegramApi->deleteSessionFile();
        $telegramApi->deletePublicFile();
        $telegramApi->deleteLogFile();
        $telegramApi->api()->logout();
    }

    public function getMessage(string $channel, TelegramApi $telegramApi): JsonResponse
    {
        return response()->json(
            (new HistoryMessage($channel))
                ->get(),
            Response::HTTP_OK
        );
    }
}

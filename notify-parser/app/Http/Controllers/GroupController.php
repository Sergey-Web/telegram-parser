<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SubscriberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class GroupController extends Controller
{
    public function downloadMessage(string $name): JsonResponse
    {
        try {
            $lastId = SubscriberService::saveMessagesGroupBatch($name);
        } catch (Throwable $e) {
            return response()->json([
                'response' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'response' => [
                'channel' => $name,
                'saved' => '~' . $lastId ?? 0,
            ],
        ]);
    }
}

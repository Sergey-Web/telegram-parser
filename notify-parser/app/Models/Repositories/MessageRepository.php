<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Message;

class MessageRepository
{
    public Message $model;

    public function __construct()
    {
        $this->model = new Message();
    }

    public function searchFullText(
        string $searchText,
        int $publisherId,
        int|string $start,
        int|string $end,
    ): ?array
    {
        return $this->model->newQuery()->from('publisher_message AS pm')
            ->selectRaw("
                message,
                m.public_id,
                m.public_date,
                m.user_name,
                m.user_last_name
            ")
            ->join('messages AS m', 'pm.message_id', '=', 'm.id')
            ->where(['pm.publisher_id' => $publisherId])
            ->whereBetween('m.public_id', [$start, $end])
            ->whereRaw("
                (search_text @@ to_tsquery('russian', '" . $searchText . "')
                OR message % '" . $searchText . "')
            ")
            ->get()
            ->toArray()
            ;
    }

    public function searchLikeMessage(
        string $searchLikeText,
        int $publisherId,
        int|string $start,
        int|string $end,
    ): array
    {
        return $this->model->newQuery()->from('publisher_message AS pm')
            ->selectRaw("
                message,
                m.public_id,
                m.public_date,
                m.user_name,
                m.user_last_name
            ")
            ->join('messages AS m', 'pm.message_id', '=', 'm.id')
            ->where(['pm.publisher_id' => $publisherId])
            ->whereBetween('m.public_id', [$start, $end])
            ->whereRaw('(' . $searchLikeText . ')')
            ->get()
            ->toArray()
            ;
    }

    public function getLastMessage(int $publisherId): ?int
    {
        $message = $this->model->newQuery()->from('publisher_message as pm')
            ->select(['m.public_id'])
            ->join('messages as m', 'pm.message_id', '=', 'm.id')
            ->where(['pm.publisher_id' => $publisherId])
            ->orderBy('m.id', 'desc')
            ->limit(1)
            ->first();

        return !empty($message->public_id) ? $message->public_id : null;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Telegram\TelegramApi;

use danog\MadelineProto\API;

class HistoryMessage implements HistoryMessageInterface
{
    private API $telegramApi;

    private int $offsetId = 0;

    private int $offsetDate = 0;

    private int $addOffset = 0;

    private int $limit = 100;

    private int $maxId = 0;

    private int $minId = 0;


    public function __construct(
        private string $peer,
    ){
        $this->telegramApi = (new TelegramApi())->api();
    }

    public function get(): array
    {
        return (array) $this->telegramApi->messages->getHistory([
            'peer' => $this->peer,
            'offset_id' => $this->offsetId,
            'offset_date' => $this->offsetDate,
            'add_offset' => $this->addOffset,
            'limit' => $this->limit,
            'max_id' => $this->maxId,
            'min_id' => $this->minId,
        ]);
    }

    public function setOffsetId(int $offsetId): static
    {
        $this->offsetId = $offsetId;

        return $this;
    }

    public function setOffsetDate(int $offsetDate): static
    {
        $this->offsetDate = $offsetDate;

        return $this;
    }

    public function setAddOffset(int $addOffset): static
    {
        $this->addOffset = $addOffset;

        return $this;
    }

    public function setLimit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function setMaxId(int $maxId): static
    {
        $this->maxId = $maxId;

        return $this;
    }

    public function setMinId(int $minId): static
    {
        $this->minId = $minId;

        return $this;
    }
}

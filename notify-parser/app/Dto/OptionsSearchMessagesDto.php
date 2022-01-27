<?php

declare(strict_types=1);

namespace App\Dto;

class OptionsSearchMessagesDto
{
    public function __construct(
        protected string $textSearch,
        protected int $publisherId,
        protected int $startMessageId,
        protected int $endMessageId,
    ){}

    public function getTextSearch(): string
    {
        return $this->textSearch;
    }

    public function getPublisherId(): int
    {
        return $this->publisherId;
    }

    public function getStartMessageId(): int
    {
        return $this->startMessageId;
    }

    public function getEndMessageId(): int
    {
        return $this->endMessageId;
    }
}

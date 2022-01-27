<?php

declare(strict_types=1);

namespace App\Dto;

class OptionsSearchRegExpMessagesDto
{
    public function __construct(
        protected string $textSearch,
        protected array $messages,
    ){}

    public function getTextSearch(): string
    {
        return $this->textSearch;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}

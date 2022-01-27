<?php

declare(strict_types=1);

namespace App\Services\Search\Types;

use App\Dto\OptionsSearchMessagesDto;
use App\Dto\OptionsSearchRegExpMessagesDto;
use App\Services\MessageService;

class FullText implements FindInterface
{
    public function find(OptionsSearchMessagesDto|OptionsSearchRegExpMessagesDto $optionsSearchMessagesDto): array
    {
        return (new MessageService())->findFullTextMessages($optionsSearchMessagesDto);
    }
}

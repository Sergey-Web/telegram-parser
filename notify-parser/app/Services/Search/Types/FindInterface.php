<?php

declare(strict_types=1);

namespace App\Services\Search\Types;

use App\Dto\OptionsSearchMessagesDto;
use App\Dto\OptionsSearchRegExpMessagesDto;

interface FindInterface
{
    public function find(
        OptionsSearchMessagesDto|OptionsSearchRegExpMessagesDto $optionsSearchMessagesDto
    ): array;
}

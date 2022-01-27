<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SenderValidator
{
    public static function toStore(array $data): array
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'type' => ['required', 'string', Rule::in(['telegram_bot'])],
        ])
            ->errors()
            ->messages()
            ;
    }

    public static function toUpdate(array $data): array
    {
        return Validator::make($data, [
            'name' => ['string', 'min:3', 'max:50'],
            'type' => ['string', Rule::in(['telegram_bot'])],
        ])
            ->errors()
            ->messages()
            ;
    }
}

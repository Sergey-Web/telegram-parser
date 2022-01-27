<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class SubscriberValidator
{
    public static function toCreate(array $data): array
    {
        $errors = [];
        foreach ($data as $subscriber) {
            $validationSub = Validator::make($subscriber, [
                'name' => ['required', 'regex:/^@/', 'string', 'max:100'],
                'sender' => ['required', 'array'],
            ]);
            if (!empty($validationSub->errors()->messages())) {
                $errors[] = $validationSub->errors()->messages();
            }

            $validationSender = SenderValidator::toStore($subscriber['sender']);

            if (!empty($validationSender)) {
                $errors['sender'] = $validationSender;
            }
        }

        return $errors;
    }

    public static function toUpdate(array $data): array
    {
        $errors = [];
        foreach ($data as $subscriber) {
            $validationSub = Validator::make($subscriber, [
                'name' => ['string', 'regex:/^@/', 'max:100'],
                'sender' => ['array'],
            ]);
            if (!empty($validationSub->errors()->messages())) {
                $errors[] = $validationSub->errors()->messages();
            }

            $validationSender = SenderValidator::toUpdate($subscriber['sender']);

            if (!empty($validationSender))   {
                $errors['sender'] = $validationSender;
            }
        }

        return $errors;
    }
}

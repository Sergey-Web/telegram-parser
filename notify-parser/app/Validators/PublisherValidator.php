<?php

declare(strict_types=1);

namespace App\Validators;

use App\Services\Search\SearchFactoryFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PublisherValidator
{
    public static function toCreate(array $data): array
    {
        $errors = [];
        foreach ($data as $publisher) {
            $validation = Validator::make($publisher, [
                'name' => ['required', 'string','max:100'],
                'type' => ['required', Rule::in(['telegram'])],
            ]);

            if (!empty($validation->errors()->messages())) {
                $errors[] = $validation->errors()->messages();
            }
        }

        return $errors;
    }

    public static function toUpdate(array $data): array
    {
        $errors = [];
        foreach ($data as $publisher) {
            $validation = Validator::make($publisher, [
                'name' => ['string','max:100'],
                'type' => [Rule::in(['telegram'])],
            ]);

            if (!empty($validation->errors()->messages())) {
                $errors[] = $validation->errors()->messages();
            }
        }

        return $errors;
    }
}

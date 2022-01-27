<?php

declare(strict_types=1);

namespace App\Validators;

use App\Models\Subscriber;
use App\Models\Task;
use App\Services\Search\SearchFactoryFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class TaskValidator
{
    public static function toStore(Request $request): array
    {
        $errors = TaskValidator::toCreate($request->all());
        $errorsPublisher = PublisherValidator::toCreate($request->publishers);
        $errorsSubscriber = SubscriberValidator::toCreate($request->subscribers);

        if (!empty($errorsPublisher)) {
            $errors['publishers'] = $errorsPublisher;
        }

        if (!empty($errorsSubscriber)) {
            $errors['subscribers'] = $errorsSubscriber;
        }

        return $errors;
    }


    public static function toUpdate(Task $task, Request $request)
    {
        $errors = Validator::make($request->all(), [
            'name' => ['min:3', 'max:50'],
            'search_text' => ['string', 'max:255'],
            'search_type' => [Rule::in(array_keys(SearchFactoryFactory::TYPES_SEARCH))],
            'publishers' => ['array'],
        ])
            ->errors()
            ->messages()
        ;

        $errorsPublisher = PublisherValidator::toUpdate($request->publishers);
        $errorsSubscriber = SubscriberValidator::toUpdate($request->subscribers);

        if (!empty($errorsPublisher)) {
            $errors['publishers'] = $errorsPublisher;
        }

        if (!empty($errorsSubscriber)) {
            $errors['subscribers'] = $errorsSubscriber;
        }

        return $errors;
    }

    public static function toCreate(array $data): array
    {
        return Validator::make($data, [
            'name' => ['required', 'unique:tasks', 'min:3', 'max:50'],
            'search_text' => ['required', 'string', 'max:255'],
            'search_type' => ['required', Rule::in(array_keys(SearchFactoryFactory::TYPES_SEARCH))],
            'publishers' => ['required', 'array'],
        ])
            ->errors()
            ->messages()
            ;
    }
}

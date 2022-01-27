<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Publisher;
use App\Models\Task;

class PublisherService
{
    public static function store(Task $task, array $publishers): Task
    {
        /** @var Publisher $publisher */
        foreach ($publishers as $publisher) {
            $publisherEntity = (new Publisher())
                ->newQuery()
                ->where(['name' => $publisher['name']])
                ->first();

            if (is_null($publisherEntity)) {
                $publisherEntity = new Publisher();
                $publisherEntity->name = $publisher['name'];
                $publisherEntity->type = $publisher['type'];

                $task->publishers()->save($publisherEntity);
            } else {
                $task->publishers()->save($publisherEntity);
            }
        }

        return $task;
    }
}

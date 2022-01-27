<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Dto\UpdateTaskDto;
use App\Models\Publisher;
use App\Models\Subscriber;
use App\Models\Task;
use App\Services\PublisherService;
use App\Services\SubscriberService;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskRepository
{
    public Task $model;

    public function __construct()
    {
        $this->model = new Task();
    }

    public function createTaskData(Request $request): Task
    {
        $this->model->name = $request->name;
        $this->model->search_text = TaskService::processSearchText($request->search_text);
        $this->model->search_type = $request->search_type;
        $this->model->save();

        $task = PublisherService::store($this->model, $request->publishers);

        return SubscriberService::store($task, $request->subscribers);
    }

    public function updateDataTask(Task $task, UpdateTaskDto $updateTaskDto): Task
    {
        $task->update($updateTaskDto->getData());

        if (!empty($request->publishers)) {
            $this->removePublisherRelations($task);
            $task = PublisherService::store($task, $request->publishers);
        }

        if (!empty($request->subscribers)) {
            $this->removeSubscriberRelations($task);
            $task = SubscriberService::store($task, $request->subscribers);
        }

        return $task;
    }

    private function removePublisherRelations(Task $task): void
    {
        /** @var Publisher $publisher */
        foreach ($task->publishers as $publisher) {
            DB::table('task_publisher')
                ->where([
                    'task_id' => $task->id,
                    'publisher_id' => $publisher->id,
                ])
                ->delete()
            ;
        }
    }

    private function removeSubscriberRelations(Task $task): void
    {
        /** @var Subscriber $subscriber */
        foreach ($task->subscribers as $subscriber) {
            DB::table('task_subscriber')
                ->where([
                    'task_id' => $task->id,
                    'subscriber_id' => $subscriber->id,
                ])
                ->delete()
            ;
        }
    }
}

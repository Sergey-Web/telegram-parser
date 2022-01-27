<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Publisher;
use App\Models\Sender;
use App\Models\Subscriber;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public static function findTaskToShow(int $id): ?array
    {
        $data = [];
        /** @var Task $task */
        $task = (new Task())->newQuery()->find($id);

        if ($task !== null) {
            $data['task']['name'] = $task->name;
            $data['task']['search_text'] = $task->search_text;
            $data['task']['search_type'] = $task->search_type;
            $data['task']['status'] = $task->status;
            $data['task']['created_at'] = $task->created_at;

            /** @var Publisher $publisher */
            foreach ($task->publishers as $keyPublisher => $publisher) {
                $data['publishers'][$keyPublisher]['name'] = $publisher->name;
                $data['publishers'][$keyPublisher]['type'] = $publisher->type;
                $data['publishers'][$keyPublisher]['status'] = $publisher->status;

                /** @var Subscriber $subscriber */
                foreach ($task->subscribers as $key => $subscriber) {
                    $data['subscribers'][$key]['name'] = $subscriber->name;
                    $data['subscribers'][$key]['status'] = $subscriber->status;

                    /** @var Sender $sender */
                    foreach ($subscriber->senders as $sender) {
                        $data['subscribers'][$key]['sender']['name'] = $sender->name;
                        $data['subscribers'][$key]['sender']['type'] = $sender->type;
                    }
                }
            }
        }

        return $data;
    }

    public static function processSearchText(string $searchText): string
    {
        return implode(' | ', explode(',', mb_strtolower($searchText)));
    }

    public static function remove(int $id): bool
    {
        $task = (new Task())->newQuery()->find($id);

        if (empty($task)) {
            return false;
        }

        if (!empty($task->subscribers)) {
            /** @var Subscriber $subscriber */
            foreach ($task->subscribers as $subscriber) {
                if (!empty($subscriber->senders)) {
                    foreach ($subscriber->senders as $sender) {
                        DB::table('subscriber_sender')
                            ->where([
                                'subscriber_id' => $subscriber->id,
                                'sender_id' => $sender->id,
                            ])
                            ->delete($task->id);
                    }
                }
            }
        }

        return (bool)$task->delete();
    }
}

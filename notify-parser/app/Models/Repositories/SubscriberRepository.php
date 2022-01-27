<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Collection;

class SubscriberRepository
{
    public Subscriber $model;

    public function __construct()
    {
        $this->model = new Subscriber();
    }

    public function getActiveSubscriber(int $taskId): ?Collection
    {
        return $this->model->newQuery()->from('task_subscriber as ts')
            ->select([
                's.name as subscriber_name',
                'sen.name as sender_name',
                'sen.type as sender_type',
            ])
            ->join('subscribers as s', 'ts.subscriber_id', '=', 's.id')
            ->join('subscriber_sender as ss', 's.id', '=', 'ss.subscriber_id')
            ->join('senders as sen', 'ss.sender_id', '=', 'sen.id')
            ->where(['ts.task_id' => $taskId])
            ->where(['s.status' => true])
            ->get()
        ;
    }
}

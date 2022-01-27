<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Publisher;
use Illuminate\Support\Collection;

class PublisherRepository
{
    public Publisher $model;

    public function __construct()
    {
        $this->model = new Publisher();
    }

    public function getActivePublishers(): ?Collection
    {
        return $this->model->newQuery()->from('tasks as t')
            ->select([
                't.id as task_id',
                't.name as task_name',
                'p.id as publisher_id',
                'p.name as publisher_name',
                'search_text',
                'search_type',
                'type',
            ])
            ->join('task_publisher as tp', 't.id', '=', 'tp.task_id')
            ->join('publishers AS p', 'tp.publisher_id', '=', 'p.id')
            ->where(['t.status' => true])
            ->where(['p.status' => true])
            ->get()
        ;
    }
}

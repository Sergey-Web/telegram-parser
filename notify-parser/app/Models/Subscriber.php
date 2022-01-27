<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $status
 */
class Subscriber extends Model
{
    use HasFactory;

    public $table = 'subscribers';

    protected $fillable = [
        'name',
        'status',
    ];

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class,'task_subscriber');
    }

    public function senders(): BelongsToMany
    {
        return $this->belongsToMany(Sender::class,'subscriber_sender');
    }
}

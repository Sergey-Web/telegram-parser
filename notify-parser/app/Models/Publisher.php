<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property bool $status
 */
class Publisher extends Model
{
    use HasFactory;

    public $table = 'publishers';

    protected $fillable = [
        'name',
        'type',
        'status',
    ];

    public function messages(): BelongsToMany
    {
        return $this->belongsToMany(Message::class,'publisher_message');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class,'task_publisher');
    }
}

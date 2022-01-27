<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $name
 * @property string $search_text
 * @property string $search_type
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 */
class Task extends Model
{
    use HasFactory;

    public $table = 'tasks';

    protected $fillable = [
        'name',
        'search_text',
        'search_type',
        'status',
        'created_at',
        'updated_at',
    ];

    public function publishers(): BelongsToMany
    {
        return $this->belongsToMany(Publisher::class,'task_publisher');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class,'task_subscriber');
    }
}

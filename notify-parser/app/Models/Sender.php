<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $name
 * @property string $type
 */
class Sender extends Model
{
    use HasFactory;

    public $table = 'senders';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
    ];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class,'subscriber_sender');
    }
}

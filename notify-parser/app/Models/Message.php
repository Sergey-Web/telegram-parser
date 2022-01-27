<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $channel_id
 * @property int $user_id
 * @property string $user_name
 * @property string $user_last_name
 * @property string $message
 * @property int $public_id
 * @property int $public_date
 * @property string $created_at
 */
class Message extends Model
{
    use HasFactory;

    public $table = 'messages';

    public $timestamps = false;

    protected $fillable = [
        'channel_id',
        'message',
        'public_id',
        'public_date',
        'date_public',
        'created_at',
    ];

    public function publishers(): BelongsToMany
    {
        return $this->belongsToMany(Publisher::class,'publisher_message');
    }
}

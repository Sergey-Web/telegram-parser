<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $name
 */
class SenderType extends Model
{
    use HasFactory;

    public $table = 'sender_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function senders(): BelongsToMany
    {
        return $this->belongsToMany(Sender::class,'sender_type');
    }
}

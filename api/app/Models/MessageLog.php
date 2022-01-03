<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'subscriber_id',
        'message',
        'created_at',
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}

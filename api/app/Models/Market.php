<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    protected $guarded = ['id'];

    protected $fillable = [
        'url',
        'title',
        'status',
        'created_at',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}

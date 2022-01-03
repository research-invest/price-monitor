<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    protected $guarded = ['id'];

    protected $fillable = [
        'url',
        'title',
        'description',
        'status',
        'marker_id',
        'created_at',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }
}

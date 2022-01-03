<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    protected $guarded = ['id'];

    protected $fillable = [
        'telegram_id',
        'first_name',
        'last_name',
        'username',
        'status',
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivotValue('status', Product::STATUS_ACTIVE);
    }
}

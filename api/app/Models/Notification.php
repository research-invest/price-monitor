<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'subscriber_id',
        'product_id',
        'price',
        'notification',
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubscriber extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'subscriber_id',
        'product_id',
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

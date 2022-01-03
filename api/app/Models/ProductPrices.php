<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrices extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'product_id',
        'price',
        'percent',
        'created_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

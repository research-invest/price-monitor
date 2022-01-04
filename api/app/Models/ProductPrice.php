<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'product_id',
        'price',
        'delta',
        'created_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use App\Markets\Markets;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProductPrice extends Model
{
    use HasFactory, Notifiable;

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

    public function scopeForActive($query)
    {
        return $query->join('products AS p', 'product_prices.product_id', '=', 'p.id')->where('p.status', '=', Markets::STATUS_ACTIVE);
    }
}

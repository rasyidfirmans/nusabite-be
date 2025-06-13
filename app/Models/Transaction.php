<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'status',
        'invoice_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_transaction')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->invoice_id = 'INV-' . str_pad(
                Transaction::max('id') + 1,
                6,
                '0',
                STR_PAD_LEFT
            ) . '-' . now()->format('Ymd');
        });
    }
}

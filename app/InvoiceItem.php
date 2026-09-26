<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'description', 'service_id',
        'quantity', 'unit_price', 'total',
    ];

    public function invoice()
    {
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }

    public function service()
    {
        return $this->belongsTo('App\Service', 'service_id');
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->total = $item->quantity * $item->unit_price;
        });
        static::saved(function ($item) {
            if ($item->invoice) {
                $item->invoice->recalculate();
            }
        });
        static::deleted(function ($item) {
            if ($item->invoice) {
                $item->invoice->recalculate();
            }
        });
    }
}

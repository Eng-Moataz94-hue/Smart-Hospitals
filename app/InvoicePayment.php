<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id', 'amount', 'method', 'received_by', 'notes',
    ];

    public function invoice()
    {
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo('App\User', 'received_by');
    }

    protected static function booted()
    {
        static::saved(function ($payment) {
            if ($payment->invoice) {
                $payment->invoice->recalculate();
            }
        });
        static::deleted(function ($payment) {
            if ($payment->invoice) {
                $payment->invoice->recalculate();
            }
        });
    }
}

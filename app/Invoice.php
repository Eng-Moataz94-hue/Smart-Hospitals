<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'patient_id', 'appointment_id',
        'total_amount', 'paid_amount', 'status', 'issued_at', 'notes',
    ];

    public function patient()
    {
        return $this->belongsTo('App\Patients', 'patient_id');
    }

    public function appointment()
    {
        return $this->belongsTo('App\Appointment', 'appointment_id');
    }

    public function items()
    {
        return $this->hasMany('App\InvoiceItem', 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany('App\InvoicePayment', 'invoice_id');
    }

    public function recalculate()
    {
        $total = $this->items()->sum('total');
        $paid = $this->payments()->sum('amount');
        $this->total_amount = $total;
        $this->paid_amount = $paid;
        if ($paid <= 0) {
            $this->status = 'unpaid';
        } elseif ($paid < $total) {
            $this->status = 'partial';
        } else {
            $this->status = 'paid';
        }
        $this->save();
        return $this;
    }

    public static function generateNumber()
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'INV-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

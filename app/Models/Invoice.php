<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'amount', 'description',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function pdf()
    {
        return $this->hasOne(\App\Models\InvoicePdf::class);
    }
    
    public function logs()
    {
        return $this->hasMany(\App\Models\InvoiceLog::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'total_budget' => 'integer',
        'progress_percent' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getPaidAmountAttribute()
    {
        return $this->invoices()->where('status', 'paid')->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->total_budget - $this->paid_amount);
    }
}

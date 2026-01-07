<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'amount',
        'investment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'investment_date' => 'date',
    ];

    /**
     * Get the investor that owns this investment.
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }
}
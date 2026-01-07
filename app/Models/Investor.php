<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'name',
        'age',
    ];

    protected $casts = [
        'investor_id' => 'integer',
        'age' => 'integer',
    ];

    /**
     * Get the investments for this investor.
     */
    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }
}
<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'coverage',
        'status',
        'is_visible',
        'sort_order',
    ];

    public function limits(): HasMany
    {
        return $this->hasMany(PlanLimit::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }
}

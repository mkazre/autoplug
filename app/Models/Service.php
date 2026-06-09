<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['name', 'category'];

    public function garageServices(): HasMany
    {
        return $this->hasMany(GarageService::class);
    }
}

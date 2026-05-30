<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    protected $fillable = ['name', 'no_rumah'];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}

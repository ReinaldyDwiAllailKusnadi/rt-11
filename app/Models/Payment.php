<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'resident_id',
        'type',
        'amount',
        'date',
        'keterangan',
        'nama_satpam',
        'bulan_list'
    ];

    protected $casts = [
        'bulan_list' => 'array',
        'amount' => 'integer'
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}

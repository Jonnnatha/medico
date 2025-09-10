<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Surgery extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'surgery_type',
        'room',
        'starts_at',
        'duration_min',
        'ends_at',
        'status',
        'is_conflict',
        'created_by',
        'confirmed_by',
        'canceled_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_conflict' => 'boolean',
        'duration_min' => 'integer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function canceledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }
}

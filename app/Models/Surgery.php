<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surgery extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'starts_at',
        'duration_min',
        'ends_at',
        'is_conflict',
        'status',
        'confirmed_by',
        'canceled_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_conflict' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (Surgery $surgery) {
            if ($surgery->starts_at && $surgery->duration_min) {
                $surgery->ends_at = $surgery->starts_at->copy()->addMinutes($surgery->duration_min);
            }

            if ($surgery->starts_at && $surgery->ends_at && $surgery->room_id) {
                $query = static::where('room_id', $surgery->room_id)
                    ->where('id', '!=', $surgery->id)
                    ->where(function ($q) use ($surgery) {
                        $q->whereBetween('starts_at', [$surgery->starts_at, $surgery->ends_at])
                          ->orWhereBetween('ends_at', [$surgery->starts_at, $surgery->ends_at])
                          ->orWhere(function ($q2) use ($surgery) {
                              $q2->where('starts_at', '<', $surgery->starts_at)
                                 ->where('ends_at', '>', $surgery->ends_at);
                          });
                    });
                $surgery->is_conflict = $query->exists();
            }
        });
    }
}

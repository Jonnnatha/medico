<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Surgery extends Model
{
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'room',
        'starts_at',
        'ends_at',
        'status',
        'surgery_type',
        'room',
        'duration_min',
        'is_conflict',
        'confirmed_by',
        'canceled_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'duration_min' => 'integer',
        'is_conflict' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function canceledBy()
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }

    protected static function booted()
    {
        static::saving(function (self $surgery) {
            if ($surgery->starts_at instanceof Carbon && $surgery->duration_min) {
                $surgery->ends_at = $surgery->starts_at->copy()->addMinutes($surgery->duration_min);
            }

            if ($surgery->doctor_id && $surgery->starts_at && $surgery->duration_min) {
                $surgery->is_conflict = self::hasConflict(
                    $surgery->doctor_id,
                    $surgery->starts_at,
                    $surgery->duration_min,
                    $surgery->id
                );
            }
        });
    }

    public static function hasConflict(int $doctorId, $startsAt, int $durationMin, ?int $ignoreId = null): bool
    {
        $starts = Carbon::parse($startsAt);
        $ends = $starts->copy()->addMinutes($durationMin);

        $query = static::where('doctor_id', $doctorId)
            ->where(function ($q) use ($starts, $ends) {
                $q->whereBetween('starts_at', [$starts, $ends])
                    ->orWhereBetween('ends_at', [$starts, $ends])
                    ->orWhere(function ($q2) use ($starts, $ends) {
                        $q2->where('starts_at', '<', $starts)
                            ->where('ends_at', '>', $ends);
                    });
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}

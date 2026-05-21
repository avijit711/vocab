<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyLog extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'words_reviewed',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function logStudy(int $userId): void
    {
        $today = now()->toDateString();

        $log = static::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($log) {
            $log->increment('words_reviewed');
        } else {
            static::create([
                'user_id' => $userId,
                'date' => $today,
                'words_reviewed' => 1,
            ]);
        }
    }

    public static function currentStreak(int $userId): int
    {
        $logs = static::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->pluck('date');

        if ($logs->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $latest = $logs->first()->toDateString();

        if ($latest !== $today && $latest !== $yesterday) {
            return 0;
        }

        foreach ($logs as $logDate) {
            $expected = now()->subDays($streak)->toDateString();
            if ($logDate->toDateString() === $expected) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Word extends Model
{
    protected $fillable = [
        'user_id',
        'english_word',
        'bangla_meaning',
        'status',
        'is_favorite',
        'read_later',
        'srs_interval',
        'srs_repetitions',
        'srs_ease_factor',
        'next_review_at',
    ];

    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
            'read_later' => 'boolean',
            'next_review_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['status' => 'learning']);
    }

    public function applySrsRating(string $rating): void
    {
        $ease = $this->srs_ease_factor;
        $interval = $this->srs_interval;
        $reps = $this->srs_repetitions;

        if ($rating === 'hard') {
            $reps = 0;
            $ease = max(1.30, $ease - 0.15);
            $interval = 1;
        } elseif ($rating === 'easy') {
            $reps += 1;
            $ease = min(3.00, $ease + 0.15);

            if ($reps === 1) {
                $interval = 1;
            } elseif ($reps === 2) {
                $interval = 3;
            } else {
                $interval = (int) round($interval * $ease);
            }
        }

        $this->update([
            'status' => 'reviewing',
            'srs_interval' => $interval,
            'srs_repetitions' => $reps,
            'srs_ease_factor' => $ease,
            'next_review_at' => now()->addDays($interval),
        ]);
    }

    public function scopeUnseen($query)
    {
        return $query->where('status', 'unseen');
    }

    public function scopeLearning($query)
    {
        return $query->where('status', 'learning');
    }

    public function scopeReviewable($query)
    {
        return $query->whereIn('status', ['learning', 'reviewing'])
            ->where(function ($q) {
                $q->whereNull('next_review_at')
                  ->orWhere('next_review_at', '<=', now());
            });
    }

    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeReadLater($query)
    {
        return $query->where('read_later', true);
    }

    public function scopeMastered($query)
    {
        return $query->where('status', 'mastered');
    }
}

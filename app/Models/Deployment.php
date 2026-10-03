<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deployment extends Model
{
    protected $fillable = [
        'user_id', 'status', 'options', 'branch', 'from_commit', 'to_commit', 'output', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'options' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'running'], true);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'running']);
    }

    /** Atomically move pending -> running. Returns false if someone else already took it. */
    public function claim(): bool
    {
        $claimed = static::whereKey($this->id)->where('status', 'pending')
            ->update(['status' => 'running', 'started_at' => now(), 'updated_at' => now()]);

        if ($claimed) {
            $this->refresh();
        }

        return (bool) $claimed;
    }

    /** A process killed mid-deploy leaves a "running" row behind; mark it failed so the lock is freed. */
    public static function releaseStale(): void
    {
        static::where('status', 'running')
            ->where('started_at', '<', now()->subSeconds((int) config('deploy.stale_after', 1800)))
            ->get()
            ->each(fn (self $d) => $d->update([
                'status' => 'failed',
                'finished_at' => now(),
                'output' => ($d->output ?? '')."\nMarked as failed: no progress for a long time.\n",
            ]));
    }

    public function duration(): ?int
    {
        return $this->started_at ? (int) $this->started_at->diffInSeconds($this->finished_at ?? now()) : null;
    }
}

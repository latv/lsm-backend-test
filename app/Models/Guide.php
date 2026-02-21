<?php

namespace App\Models;

use App\Enums\ChannelTVShowCount;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Guide extends Model
{
    protected $table = 'guide';

    protected $fillable = [
        'title',
        'channel_nr',
        'starts_at',
        'ends_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static function booted(): void
    {
        $flush = function (self $guide): void {
            $date = Carbon::parse($guide->starts_at)->toDateString();

            Cache::forget("guide_channel_{$guide->channel_nr}_date_{$date}");

            foreach (ChannelTVShowCount::values() as $limit) {
                Cache::forget("upcoming_guides_channel_{$guide->channel_nr}_limit_{$limit}");
            }
        };

        static::created($flush);
        static::updated($flush);
        static::deleted($flush);
    }

    public function scopeForTvDay(Builder $query, string $date): Builder
    {
        $start = Carbon::parse($date)->setTime(6, 0, 0);
        $end = $start->copy()->addDay();
        $nextDayStart = $end->copy()->addDay();

        // Get the ID of the first show of the next TV day,
        // so we can calculaate the end time of the last show of the current TV day
        $firstNextDayId = (clone $query->getQuery())
            ->where('starts_at', '>=', $end)
            ->where('starts_at', '<', $nextDayStart)
            ->orderBy('starts_at', 'asc')
            ->limit(1)
            ->value('id');

        return $query->where(function (Builder $q) use ($start, $end) {
            $q->where('starts_at', '>=', $start)
                ->where('starts_at', '<', $end);
        })
            ->when($firstNextDayId, fn ($q) => $q->orWhere('id', $firstNextDayId));
    }
}

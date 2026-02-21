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

        return $query->where('starts_at', '>=', $start)
            ->where('starts_at', '<', $end);
    }
}

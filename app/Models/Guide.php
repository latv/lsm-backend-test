<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    protected $table = 'guide';

    protected $fillable = [
        'title',
        'channel_nr',
        'starts_at',
        'ends_at',
    ];

    public function scopeForTvDay(Builder $query, string $date): Builder
    {
        $start = Carbon::parse($date)->setTime(6, 0, 0);
        $end = $start->copy()->addDay();

        return $query->where('starts_at', '>=', $start)
            ->where('starts_at', '<', $end);
    }
}

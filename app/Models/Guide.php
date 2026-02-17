<?php

namespace App\Models;

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }
}
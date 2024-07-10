<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventPackage extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}

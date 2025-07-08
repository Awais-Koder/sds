<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmittelFinalReport extends Model
{
    protected $guarded = [];

    public function submittel(): BelongsTo
    {
        return $this->belongsTo(Submittel::class, 'submittel_id');
    }
}

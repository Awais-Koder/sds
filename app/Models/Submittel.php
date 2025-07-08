<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submittel extends Model
{
    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Submittel::class, 'parent_submittel_id');
    }

    public function children() : HasMany
    {
        return $this->hasMany(Submittel::class, 'parent_submittel_id');
    }

    public function outgoings() : HasMany
    {
        return $this->hasMany(Outgoing::class);
    }
    public function incomings() : HasMany
    {
        return $this->hasMany(Incoming::class);
    }
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
    public function approved(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function finalReports(): HasMany
    {
        return $this->hasMany(SubmittelFinalReport::class);
    }
}

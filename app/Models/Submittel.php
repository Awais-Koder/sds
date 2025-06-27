<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submittel extends Model
{
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Submittel::class, 'parent_submittel_id');
    }

    public function children()
    {
        return $this->hasMany(Submittel::class, 'parent_submittel_id');
    }

    public function outgoings()
    {
        return $this->hasMany(Outgoing::class);
    }
    public function incomings()
    {
        return $this->hasMany(Incoming::class);
    }
}

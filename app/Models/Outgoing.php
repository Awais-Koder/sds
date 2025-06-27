<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outgoing extends Model
{
    protected $guarded = [];

    public function submittel()
    {
        return $this->belongsTo(Submittel::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

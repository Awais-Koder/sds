<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incoming extends Model
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
    public function user()
    {
        return $this->belongsTo(User::class , 'submitted_by');
    }
    public function approved()
    {
        return $this->belongsTo(User::class , 'approved_by');
    }
}

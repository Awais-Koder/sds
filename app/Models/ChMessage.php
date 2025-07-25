<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Chatify\Traits\UUID;

class ChMessage extends Model
{
    use UUID;


    public function from()
    {
        return $this->belongsTo(User::class , 'from_id');
    }
    public function to()
    {
        return $this->belongsTo(User::class , 'to_id');
    }
}

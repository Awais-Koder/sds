<?php

namespace app\Services;

use App\Models\Setting;

class DefaultService
{
    public static function getSetings()
    {
        return Setting::first();
    }
}

<?php

namespace App\Libs;

use App\Models\Autonumber;
use App\Models\BatchAutonumber;
use App\Models\Configuration;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MiscUtility
{
    public static function toFloat($raw){
        $valueStr1 = str_replace('.','', $raw);
        $valueStr2 = $valueStr1;
        if(str_contains($valueStr1, ',')){
            $valueStr2 = str_replace(',', '.', $valueStr1);
        }

        return (double) $valueStr2;
    }

    public static function generateRandomShortString($length){
        return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),1,$length);
    }
}

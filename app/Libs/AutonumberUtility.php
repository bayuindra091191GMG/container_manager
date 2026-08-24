<?php

namespace App\Libs;

use App\Models\Autonumber;
use App\Models\BatchAutonumber;
use App\Models\Configuration;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoNumberUtility
{
    public static function generateAutonumber($code)
    {
        $autonumberDB = Autonumber::where('doc_code', $code)->first();
        $datenow = Carbon::now();

        if(intval($autonumberDB->current_month) != $datenow->month){
            $autonumberDB->current_month = $datenow->month;
            $autonumberDB->next_no = 1;
            $autonumberDB->save();
        }

        if(intval($autonumberDB->current_year) != $datenow->year){
            $autonumberDB->current_year = $datenow->year;
            $autonumberDB->next_no = 1;
            $autonumberDB->save();
        }

        $number = str_pad($autonumberDB->next_no, 5,'0', STR_PAD_LEFT);

        $result = strtoupper($code) . '-'.$datenow->format('Y') .$datenow->format('m') .$number;

        $autonumberDB->next_no++;
        $autonumberDB->save();

        return $result;
    }
}

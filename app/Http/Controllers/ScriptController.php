<?php

namespace App\Http\Controllers;

use App\Libs\AutoNumberUtility;
use App\Models\ContainerData;
use App\Models\DoHeader;
use App\Models\TimbanganData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScriptController extends Controller
{
    public function fillBranchDestinationId()
    {
        $doHeaders = DoHeader::all();
        foreach ($doHeaders as $doHeader) {
            $tmpId = DB::table('branch_recipients')
                ->where('code', '=', $doHeader->shipto_code)
                ->value('branch_id');

            $doHeader->branch_destination_id = $tmpId;
            $doHeader->save();
        }

        return 'SUCCESS';
    }

    public function syncSiToContainer()
    {
        $doHeaders = DB::table('do_headers')->get();
        foreach ($doHeaders as $doHeader) {
            if (! empty($doHeader->container1_code)) {
                $containerMaster = ContainerData::where('code', '=', $doHeader->container1_code)->first();
                $containerMaster->do_id = $doHeader->id;
                $containerMaster->do_code = $doHeader->code;

                $siMonitor = DB::table('shipping_instruction_monitors')
                    ->where('container_code', '=', $doHeader->container1_code)
                    ->first();
                if (! empty($siMonitor)) {
                    $containerMaster->status_id = $siMonitor->status_id;
                } else {
                    $containerMaster->status_id = 7;
                }

                $containerMaster->save();
            }
        }

        return 'SUCCESS';
    }

    public function simulateTimbanganIn(Request $request)
    {
        try {
            $newTimbanganData = new TimbanganData;
            $newTimbanganData->branch_id = Auth::user()->branch_id ?? null;
            $newTimbanganData->code = AutoNumberUtility::generateAutonumber('TD');
            $newTimbanganData->container_no = $request->query('container_code');
            $newTimbanganData->weight_type = 'TYPE';
            $newTimbanganData->weight_1 = 2000;
            $newTimbanganData->weight_1_datetime = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $newTimbanganData->weight_2 = 0;
            $newTimbanganData->berat_bruto = 0;
            $newTimbanganData->berat_netto = 0;
            $newTimbanganData->status_id = 18;
            $newTimbanganData->driver_name = 'Pak Udin';
            $newTimbanganData->created_by = 1;
            $newTimbanganData->updated_by = 1;
            $newTimbanganData->is_sync = false;
            $newTimbanganData->save();

            $containerMaster = ContainerData::where('code', '=', $request->query('container_code'))->first();
            $containerMaster->warehouse_location_id = 18;
            $containerMaster->warehouse_location_status = 'RECEIVED AT WH';

            // End free storage time
            $containerMaster->is_storage_monitor_end = true;

            // Start free demurrage time
            $containerMaster->is_demurrage_monitor_start = true;
            $containerMaster->free_demurrage_start_at = Carbon::today('Asia/Jakarta')->toDateTimeString();
            $containerMaster->free_demurrage_end_at = Carbon::today('Asia/Jakarta')
                ->addDays($containerMaster->free_demurrage_duration)
                ->toDateTimeString();

            $containerMaster->received_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $containerMaster->status_id = 18;
            $containerMaster->is_sync = false;
            $containerMaster->save();

            DB::table('shipping_instruction_monitors')
                ->where('header_code', '=', $containerMaster->si_code)
                ->where('container_code', '=', $containerMaster->code)
                ->update([
                    'status_id' => 18,
                    'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    'is_sync' => 0,
                ]);

            $updatedSiMonitors = DB::table('shipping_instruction_monitors')
                ->where('header_code', '=', $containerMaster->si_code)
                ->get();

            $isAllUpdated = true;
            foreach ($updatedSiMonitors as $siMonitor) {
                if ($siMonitor->status_id !== 18) {
                    $isAllUpdated = false;
                    break;
                }
            }

            if ($isAllUpdated) {
                DB::table('shipping_instruction_headers')
                    ->where('code', '=', $containerMaster->si_code)
                    ->update([
                        'status_id' => 18,
                        'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                        'is_sync' => 0,
                    ]);
            }

            return 'SUCCCESS';
        } catch (\Exception $ex) {
            dd($ex);
        }
    }
}

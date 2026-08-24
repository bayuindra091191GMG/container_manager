<?php

namespace App\Console\Commands;

use App\Models\ContainerData;
use App\Models\DoDetail;
use App\Models\DoHeader;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DoSynchronizeCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:do-synchronize-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $startDate = Carbon::today('Asia/Jakarta')->subDays(30)->toDateTimeString();
            $endDate = Carbon::today('Asia/Jakarta')->addDays(1)->toDateTimeString();

            $externalQueries = DB::connection('oracle')
                ->table('V_CMS')
                ->whereBetween('tgl', array($startDate, $endDate))
                ->whereNotNull('container1')
                ->get();

            foreach($externalQueries as $externalQuery){
                if(!empty($externalQuery->no_transfer)){
                    if($externalQuery->unit === 'TON'){
                        if(DB::table('do_headers')
                            ->where('code', '=', $externalQuery->no_transfer)
                            ->exists()){
                            continue;
                        }
                        else{
                            $type = 'transfer';
                            $documentNo = $externalQuery->no_transfer;
                        }
                    }
                    else{
                        continue;
                    }
                }
                else{
                    if(!empty($externalQuery->no_do)){
                        if(DB::table('do_headers')
                            ->where('code', '=', $externalQuery->no_do)
                            ->exists()){
                            continue;
                        }
                        else{
                            $type = 'delivery_order';
                            $documentNo = $externalQuery->no_do;
                        }
                    }
                    else{
                        continue;
                    }
                }

                // Check distributor
                $branchDestination = DB::table('branch_recipients')
                    ->where('code', '=', $externalQuery->shipto)
                    ->first();

                if(empty($branchDestination)){
                    Log::channel('cron')->info('DoSynchronizeCron code: '. $documentNo.
                        ' type: '. $type.
                        ' shipto: '. $externalQuery->shipto.
                        ' DISTRIBUTOR NOT FOUND');

                    continue;
                }

                $branchDestinationId = $branchDestination->branch_id;
                if($branchDestinationId == 1 && $externalQuery->customer != $externalQuery->shipto){
                    $branchDb = DB::table('branches')
                        ->where('code', '=', 'MDND')
                        ->first();
                    $branchDestinationId = $branchDb->id;
                }

                $newDoHeader = new DoHeader();
                $newDoHeader->code = $documentNo;
                $newDoHeader->type = $type;
                $newDoHeader->date = $externalQuery->tgl;
                $newDoHeader->so_code = $externalQuery->no_so;
                $newDoHeader->dr_code = $externalQuery->dr_no;
                $newDoHeader->shipping_company = $externalQuery->shipping_company;
                $newDoHeader->customer_code = $externalQuery->customer;
                $newDoHeader->customer_name = $externalQuery->customer_name;

                $newDoHeader->branch_destination_id = $branchDestinationId;
                $newDoHeader->shipto_code = $externalQuery->shipto;
                $newDoHeader->shipto_name = $externalQuery->shipto_name;
                $newDoHeader->transportation = $externalQuery->angkutan;
                $newDoHeader->status_id = 1;
                $newDoHeader->is_close = false;
                $newDoHeader->save();

                // Parsing container1
                if(!empty($externalQuery->container1)){
                    $container1Code = $externalQuery->container1;
                    $needle = null;
                    if(str_contains($externalQuery->container1, '|')){
                        $needle = '|';
                    }
                    elseif(str_contains($externalQuery->container1, '/')){
                        $needle = '/';
                    }

                    if(!empty($needle)){
                        $tmpArr = explode($needle, $externalQuery->container1);
                        $container1Code = trim($tmpArr[0]);

                    }

                    $newDoHeader->container1_code = $container1Code;

                    // Check container synched
                    $existingContainer1 = DB::table('container_datas')
                        ->where('code', '=', $container1Code)
                        ->first();

                    if(!empty($existingContainer1)){
                        $container1Id = $existingContainer1->id;

                        DB::table('container_datas')
                            ->where('id', '=', $container1Id)
                            ->update([
                                'do_id' => $newDoHeader->id,
                                'do_code' => $newDoHeader->code,
                                'do_type' => $newDoHeader->type,
                                'timbangan_id' => null,
                                'timbangan_code' => null,
                                'status_id' => 7,
                                'warehouse_location_id' => 13,
                                'warehouse_location_status' => 'DOORING',
                                'warehouse_area' => 'B',
                                'warehouse_area_row' => 'B1',
                                'warehouse_status_id' => 3,
                                'warehouse_status' => 'FULL',
                                'warehouse_status_reason' => null,
                                'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                                'si_destination_id' => null,
                                'si_destination_name' => null,
                                'si_etd_date' => null,
                                'si_eta_date' => null,
                                'free_storage_start_at' => null,
                                'free_storage_end_at' => null,
                                'free_demurrage_start_at' => null,
                                'free_demurrage_end_at' => null,
                                'last_status_updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                                'berthing_at' => null,
                                'dooring_at' => null,
                                'received_at' => null,
                                'is_over_storage' => 0,
                                'returned_at' => null,
                                'is_over_demurrage' => 0,
                                'free_storage_duration' => 0,
                                'free_demurrage_duration' => 0,
                                'is_storage_monitor_start' => 0,
                                'is_storage_monitor_end' => 0,
                                'is_demurrage_monitor_start' => 0,
                                'is_demurrage_monitor_end' => 0,
                                'is_sync' => 0,
                                'item_name' => $externalQuery->item_name,
                                'vessel_name' => null
                            ]);

                            Log::channel('cron')->info('DoSynchronizeCron code: '. $documentNo.
                            ' type: '. $type.
                            ' container1_code: '. $container1Code.
                            ' updated_container1_id: '. $container1Id.
                            ' CONTAINER UPDATED');
                    }
                    else{
                        $newContainer1 = new ContainerData();
                        $newContainer1->code = $container1Code;
                        $newContainer1->do_id = $newDoHeader->id;
                        $newContainer1->do_code = $documentNo;
                        $newContainer1->status_id = 7;
                        $newContainer1->warehouse_location_status = 'DOORING';
                        $newContainer1->item_name = $externalQuery->item_name;
                        $newContainer1->save();

                        Log::channel('cron')->info('DoSynchronizeCron code: '. $documentNo.
                            ' type: '. $type.
                            ' container1_code: '. $container1Code.
                            ' created_container1_id: '. $newContainer1->id.
                            ' CONTAINER CREATED');

                        $container1Id = $newContainer1->id;
                    }

                    $newDoHeader->container1_id = $container1Id;
                }

                // Parsing container2
                if(!empty($externalQuery->container2)){
                    $container2Code = $externalQuery->container2;
                    $needle = null;
                    if(str_contains($externalQuery->container2, '|')){
                        $needle = '|';
                    }
                    elseif(str_contains($externalQuery->container2, '/')){
                        $needle = '/';
                    }

                    if(!empty($needle)){
                        $tmpArr = explode($needle, $externalQuery->container2);
                        $container2Code = trim($tmpArr[0]);

                    }

                    $newDoHeader->container2_code = $container2Code;

                    // Check container synched
                    $existingContainer2 = DB::table('container_datas')
                        ->where('code', '=', $container2Code)
                        ->first();

                    if(!empty($existingContainer2)){
                        $container2Id = $existingContainer2->id;

                        DB::table('container_datas')
                            ->where('id', '=', $container2Id)
                            ->update([
                                'do_id' => $newDoHeader->id,
                                'do_code' => $newDoHeader->code,
                                'timbangan_id' => null,
                                'timbangan_code' => null,
                                'status_id' => 7,
                                'warehouse_location_id' => 13,
                                'warehouse_location_status' => 'DOORING',
                                'warehouse_area' => 'B',
                                'warehouse_area_row' => 'B1',
                                'warehouse_status_id' => 3,
                                'warehouse_status' => 'FULL',
                                'warehouse_status_reason' => null,
                                'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                                'si_destination_id' => null,
                                'si_destination_name' => null,
                                'si_etd_date' => null,
                                'si_eta_date' => null,
                                'free_storage_start_at' => null,
                                'free_storage_end_at' => null,
                                'free_demurrage_start_at' => null,
                                'free_demurrage_end_at' => null,
                                'last_status_updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                                'received_at' => null,
                                'is_over_storage' => 0,
                                'returned_at' => null,
                                'is_over_demurrage' => 0,
                                'free_storage_duration' => 0,
                                'free_demurrage_duration' => 0,
                                'is_storage_monitor_start' => 0,
                                'is_storage_monitor_end' => 0,
                                'is_demurrage_monitor_start' => 0,
                                'is_demurrage_monitor_end' => 0,
                                'is_sync' => 0,
                                'item_name' => $externalQuery->item_name
                            ]);

                            Log::channel('cron')->info('DoSynchronizeCron code: '. $documentNo.
                            ' type: '. $type.
                            ' container2_code: '. $container2Code.
                            ' updated_container2_id: '. $container2Id.
                            ' CONTAINER UPDATED');
                    }
                    else{
                        $newContainer2 = new ContainerData();
                        $newContainer2->code = $container2Code;
                        $newContainer2->do_id = $newDoHeader->id;
                        $newContainer2->do_code = $documentNo;
                        $newContainer2->status_id = 7;
                        $newContainer2->warehouse_location_status = 'DOORING';
                        $newContainer2->item_name = $externalQuery->item_name;
                        $newContainer2->save();

                        Log::channel('cron')->info('DoSynchronizeCron code: '. $documentNo.
                            ' type: '. $type.
                            ' container2_code: '. $container2Code.
                            ' created_container2_id: '. $newContainer2->id.
                            ' CONTAINER CREATED');

                        $container2Id = $newContainer2->id;
                    }

                    $newDoHeader->container2_id = $container2Id;
                }

                $newDoHeader->save();

                $existingItem = DB::table('items')
                    ->where('code', '=', $externalQuery->item_code)
                    ->first();

                // Check item synched
                if(!empty($existingItem)){
                    $itemId = $existingItem->id;
                }
                else{
                    $newItem = new Item();
                    $newItem->code = $externalQuery->item_code;
                    $newItem->name = $externalQuery->item_name;
                    $newItem->status_id = 1;
                    $newItem->save();

                    Log::channel('cron')->info('DoSynchronizeCron code: '. $documentNo.
                    ' type: '. $type.
                    ' item_code: '. $externalQuery->item_code.
                    ' created_item_id: '. $newItem->id.
                    ' ITEM CREATED');

                    $itemId = $newItem->id;
                }

                $newDoDetail = new DoDetail();
                $newDoDetail->header_id = $newDoHeader->id;
                $newDoDetail->transfer_no = $newDoHeader->code;
                $newDoDetail->item_id = $itemId;
                $newDoDetail->item_code = $externalQuery->item_code;
                $newDoDetail->item_name = $externalQuery->item_name;
                $newDoDetail->qty = $externalQuery->qty;
                $newDoDetail->unit = $externalQuery->unit;
                $newDoDetail->save();

                Log::channel('cron')->info('DoSynchronizeCron code: '. $documentNo.
                    ' type: '. $type.
                    ' customer_code: '. $externalQuery->customer.
                    ' item_code: '. $externalQuery->item_code.
                    ' created_do_id: '. $newDoHeader->id.
                    ' DO HEADER CREATED');
            }
        }
        catch(\Exception $ex){
            Log::channel('cron')->error('DoSynchronizeCron error EX: '. $ex);
            return 0;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\ContainerData;
use App\Models\ContainerHistory;
use App\Models\DoDetail;
use App\Models\DoHeader;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InputContainerHistoryCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:input-container-history-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(){
        try{
            $containers = DB::table('container_datas')
                ->whereNotNull('returned_at')
                ->get();

            foreach($containers as $container){
                if(DB::table('container_histories')
                    ->where('do_id', '=', $container->do_id)
                    ->exists()){
                    continue;
                }

                // Get days since berthing data
                $today = Carbon::today('Asia/Jakarta');
                $berthingDate = Carbon::parse($container->berthing_at);

                $daySinceBerthing = $today->diffInDays($berthingDate);

                $newHistory = new ContainerHistory();
                $newHistory->code = $container->code;
                $newHistory->do_id = $container->do_id;
                $newHistory->do_code = $container->do_code;
                $newHistory->do_type = $container->do_type;
                $newHistory->item_name = $container->item_name;
                $newHistory->vessel_name = $container->vessel_name;
                $newHistory->timbangan_id = $container->timbangan_id;
                $newHistory->timbangan_code = $container->timbangan_code;
                $newHistory->timbangan_ceramic_code = $container->timbangan_ceramic_code;
                $newHistory->si_code = $container->si_code;
                $newHistory->si_destination_id = $container->si_destination_id;
                $newHistory->si_destination_name = $container->si_destination_name;
                $newHistory->si_etd_date = $container->si_etd_date;
                $newHistory->si_eta_date = $container->si_eta_date;
                $newHistory->free_storage_duration = $container->free_storage_duration;
                $newHistory->free_demurrage_duration = $container->free_demurrage_duration;
                $newHistory->free_storage_start_at = $container->free_storage_start_at;
                $newHistory->free_storage_end_at = $container->free_storage_end_at;
                $newHistory->free_demurrage_start_at = $container->free_demurrage_start_at;
                $newHistory->free_demurrage_end_at = $container->free_demurrage_end_at;
                $newHistory->is_over_storage = $container->is_over_storage;
                $newHistory->is_over_demurrage = $container->is_over_demurrage;
                $newHistory->berthing_at = $container->berthing_at;
                $newHistory->dooring_at = $container->dooring_at;
                $newHistory->received_at = $container->received_at;
                $newHistory->returned_at = $container->returned_at;
                $newHistory->semen_out_at = $container->semen_out_at;
                $newHistory->ceramic_received_at = $container->ceramic_received_at;
                $newHistory->ceramic_out_at = $container->ceramic_out_at;
                $newHistory->days_since_berthing = $daySinceBerthing;
                $newHistory->save();

                Log::channel('cron')->info('InputContainerHistoryCron code: '. $container->code.
                            ' do_type: '. $container->do_type.
                            ' do_code: '. $container->do_code.
                            ' HISTORY CREATED');
            }
        }
        catch(\Exception $ex){
            Log::channel('cron')->error('InputContainerHistoryCron error EX: '. $ex);
            return 0;
        }
    }
}

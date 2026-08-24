<?php

namespace App\Console\Commands;

use App\Models\ContainerData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverDemurrageCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-over-demurrage-cron';

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
            Log::channel('cron')->info('CheckOverDemurrageCron START');

            $containers = ContainerData::whereNotNull('si_destination_id')
                ->get();

            foreach($containers as $container){
                $today = Carbon::today('Asia/Jakarta');

                // Check over storage
                if($container->is_storage_monitor_start && $container->is_storage_monitor_end){
                    $storageMonitorEnd = Carbon::parse($container->free_storage_end_at);
                    if($today->greaterThan($storageMonitorEnd)){
                        $container->is_over_storage = true;
                        $container->save();

                        Log::channel('cron')->info('CheckOverDemurrageCron container_code: '. $container->code.
                            ' si_destination_name: '. $container->si_destination_name.
                            ' free_storage_end_at: '. $container->free_storage_end_at.
                            ' CONTAINER OVER STORAGE');
                    }
                }

                // Check over demurrage
                if($container->is_demurrage_monitor_start && $container->is_demurrage_monitor_end){
                    $demurrageMonitorEnd = Carbon::parse($container->free_demurrage_end_at);
                    if($today->greaterThan($demurrageMonitorEnd)){
                        $container->is_over_demurrage = true;
                        $container->save();

                        Log::channel('cron')->info('CheckOverDemurrageCron container_code: '. $container->code.
                            ' si_destination_name: '. $container->si_destination_name.
                            ' free_demurrage_end_at: '. $container->free_demurrage_end_at.
                            ' CONTAINER OVER DEMURRAGE');
                    }
                }
            }

            Log::channel('cron')->info('CheckOverDemurrageCron END');
        }
        catch(\Exception $ex){
            Log::channel('cron')->error('CheckOverDemurrageCron error EX: '. $ex);
            return 0;
        }
    }
}

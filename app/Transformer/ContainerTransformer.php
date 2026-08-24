<?php


namespace App\Transformer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ContainerTransformer extends TransformerAbstract
{
    public function transform($container){
        $date = Carbon::parse($container->created_at)->toIso8601String();
        $etdDate = !empty($container->si_etd_date) ? Carbon::parse($container->si_etd_date)->toIso8601String() : '';
        $etaDate = !empty($container->si_eta_date) ? Carbon::parse($container->si_eta_date)->toIso8601String() : '';
        $berthingDate = !empty($container->berthing_at) ? Carbon::parse($container->berthing_at)->toIso8601String() : '';
        $dooringDate = !empty($container->dooring_at) ? Carbon::parse($container->dooring_at)->toIso8601String() : '';
        $receivedDate = !empty($container->received_at) ? Carbon::parse($container->received_at)->toIso8601String() : '';
        $semenOutDate = !empty($container->semen_out_at) ? Carbon::parse($container->semen_out_at)->toIso8601String() : '';
        $ceramicReceivedAt = !empty($container->ceramic_received_at) ? Carbon::parse($container->ceramic_received_at)->toIso8601String() : '';
        $returnedDate = !empty($container->returned_at) ? Carbon::parse($container->returned_at)->toIso8601String() : '';

        return[
            'created_at' => $date,
            'code' => $container->code ?? '-',
            'do_code' => $container->do_code,
            'timbangan_code' => $container->timbangan_code,
            'warehouse_location_status' => $container->warehouse_location_status ?? '-',
            'warehouse_status' => $container->warehouse_status ?? '-',
            'warehouse_area' => $container->warehouse_area ?? '-',
            'warehouse_area_row' => $container->warehouse_area_row ?? '-',
            'item_name' => $container->item_name,
            'berthing_at' => $berthingDate,
            'dooring_at' => $dooringDate,
            'received_at' => $receivedDate,
            'semen_out_at' => $semenOutDate,
            'ceramic_received_at' => $ceramicReceivedAt,
            'returned_at' => $returnedDate,
            'vessel_name' => $container->vessel_name,
            'status' => $container->status,
            'si_destination_id' => $container->si_destination_id,
            'si_destination_name' => $container->si_destination_name,
            'si_etd_date' => $etdDate,
            'si_eta_date' => $etaDate,
            'days_since_berthing_to_returned' => $container->days_since_berthing_to_returned ?? 0,
            'days_since_berthing' => $container->days_since_berthing ?? 0,
            'free_storage_time_limit' => $container->free_storage_time_limit,
            'free_demurrage_time_limit' => $container->free_demurrage_time_limit ?? 0,
            'is_storage_monitor_start' => $container->is_storage_monitor_start,
            'is_storage_monitor_end' => $container->is_storage_monitor_end,
            'is_demurrage_monitor_start' => $container->is_demurrage_monitor_start,
            'is_demurrage_monitor_end' => $container->is_demurrage_monitor_end
        ];
    }
}

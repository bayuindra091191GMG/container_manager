<?php


namespace App\Transformer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ShippingInstructionTransformer extends TransformerAbstract
{
    public function transform($data){
        $createdAt = Carbon::parse($data->created_at)->toIso8601String();

        $sailingDate  = '';
        if(!empty($data->sailing_date)){
            $sailingDate = Carbon::parse($data->sailing_date)->toIso8601String();
        }

        $dockingDate  = '';
        if(!empty($data->docking_date)){
            $dockingDate = Carbon::parse($data->docking_date)->toIso8601String();
        }


        $dooringDate = !empty($data->dooring_at) ? Carbon::parse($data->dooring_at)->toIso8601String() : '';
        $receivedDate = !empty($data->received_at) ? Carbon::parse($data->received_at)->toIso8601String() : '';

        $btn = '<a class="btn btn-xs btn-info" href="'. route('shipping_instruction.show', $data->id) .'"><i class="fa fa-fw fa-eye"></i></a>';

        return[
            'action' => $btn,
            'created_at' => $createdAt,
            'etd_date' => $data->etd_date,
            'eta_date' => $data->eta_date,
            'id' => $data->id,
            'code' => $data->code,
            'destination' => $data->destination,
            'shipper' => $data->shipper,
            'vessel_name' => $data->vessel_name,
            'sailing_date' => $sailingDate,
            'docking_date' => $dockingDate,
            'dooring_at' => $dooringDate,
            'received_at' => $receivedDate,
            'status_display' => $data->status_display,
            'is_sync' => $data->is_sync,
            'total_berthing_cont' => $data->total_berthing_cont,
            'total_dooring_cont' => $data->total_dooring_cont,
            'total_received_cont' => $data->total_received_cont
        ];
    }
}

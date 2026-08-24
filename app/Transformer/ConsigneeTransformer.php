<?php


namespace App\Transformer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ConsigneeTransformer extends TransformerAbstract
{
    public function transform($consignee){
        $date = Carbon::parse($consignee->created_at)->toIso8601String();
        // $user = Auth::user();
        $action = "";

        if($consignee->status_id != 2){
            if(Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('consignee.show')){
                $action .= '<a class="btn btn-xs btn-info" href="'. route('consignee.show', $consignee->id) .'"><i class="fa fa-fw fa-eye"></i></a>';
            }

            if(Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('consignee.edit')){
                $action .= '&nbsp;<a class="btn btn-xs btn-secondary" href="'. route('consignee.edit', $consignee->id) .'"><i class="fa fa-fw fa-edit"></i></a>';
            }
        }

        return[
            'created_at' => $date,
            'name' => $consignee->name ?? '-',
            'code' => $consignee->code,
            // 'location'      => $consignee->location ?? '-',
            // 'province_id'   => $consignee->province_id,
            // 'province_name' => $consignee->province_name ?? "-",
            'branch_name' => $consignee->branch_name,
            // 'description'   => $consignee->description ?? '-',
            'action' => $action
        ];
    }
}

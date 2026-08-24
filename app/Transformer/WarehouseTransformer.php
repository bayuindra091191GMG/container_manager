<?php


namespace App\Transformer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class WarehouseTransformer extends TransformerAbstract
{
    public function transform($warehouse){
        $date = Carbon::parse($warehouse->created_at)->toIso8601String();
        $action = "";

        if($warehouse->status_id != 2){
            if(Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('warehouse.show')){
                $action .= '<a class="btn btn-xs btn-info" href="'. route('warehouse.show', $warehouse->id) .'"><i class="fa fa-fw fa-eye"></i></a>';
            }

            if(Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('warehouse.edit')){
                $action .= '&nbsp;<a class="btn btn-xs btn-secondary" href="'. route('warehouse.edit', $warehouse->id) .'"><i class="fa fa-fw fa-edit"></i></a>';
            }
        }

        return[
            'created_at'    => $date,
            'name'          => $warehouse->name ?? '-',
            'code'          => $warehouse->code,
            'branch'        => $warehouse->branch->name,
            'description'   => $warehouse->description ?? '-',
            'action'        => $action
        ];
    }
}

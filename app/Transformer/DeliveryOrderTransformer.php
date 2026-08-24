<?php

namespace App\Transformer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;

class DeliveryOrderTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        $date = Carbon::parse($data->date)->toIso8601String();

        $details = DB::table('do_details')
            ->where('header_id', '=', $data->id)
            ->get();

        $detailArr = [];
        foreach ($details as $detail) {
            $detailArr[] = [
                'item_code' => $detail->item_code,
                'item_name' => $detail->item_name,
                'qty' => $detail->qty,
                'unit' => $detail->unit,
            ];
        }

        $btn = '';
        if ($data->status_id != 21) {
            if (Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('do.show')) {
                $btn .= '<a class="btn btn-xs btn-info" href="'.route('delivery_order.show', $data->id).'"><i class="fa fa-fw fa-eye"></i></a>';
            }

            if (! $data->is_close) {
                if (Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('do.edit')) {
                    $btn .= '&nbsp;<a class="btn btn-xs btn-secondary" href="'.route('delivery_order.edit', $data->id).'"><i class="fa fa-fw fa-edit"></i></a>';
                }

                if (Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('do.delete')) {
                    $btn .= '&nbsp;<button class="btn btn-xs btn-danger js-void-do" data-id="'.$data->id.'"><i class="fa fa-fw fa-trash"></i></button>';
                }
            }
        }

        return [
            'id' => $data->id,
            'code' => $data->code,
            'type' => $data->type,
            'date' => $date,
            'so_code' => $data->so_code,
            'dr_code' => $data->dr_code,
            'customer_code' => $data->customer_code,
            'customer_name' => $data->customer_name,
            'branch_name' => $data->branch_name,
            'shipto_code' => $data->shipto_code,
            'shipto_name' => $data->shipto_name,
            'shipping_company' => $data->shipping_company,
            'container1_code' => $data->container1_code,
            'container2_code' => $data->container2_code,
            'transportation' => $data->transportation,
            'is_close' => $data->is_close,
            'status_id' => $data->status_id,
            'details' => $detailArr,
            'action' => $btn,
        ];
    }
}

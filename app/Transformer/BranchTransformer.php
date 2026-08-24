<?php


namespace App\Transformer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class BranchTransformer extends TransformerAbstract
{
    public function transform($data){
        $btn = '';
        if($data->status_id != 2){
            if(Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('branch.show')){
                $btn .= '<a class="btn btn-xs btn-info" href="'. route('branch.show', $data->id) .'"><i class="fa fa-fw fa-eye"></i></a>';
            }

            if(Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('branch.edit')){
                $btn .= '&nbsp;<a class="btn btn-xs btn-secondary" href="'. route('branch.edit', $data->id) .'"><i class="fa fa-fw fa-edit"></i></a>';
            }
//            $btn .= '<button class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled delete-btn" data-bs-toggle="tooltip" title="Delete" data-url="'. route('accounting.coa.delete', $row->id) .'"><i class="fa fa-fw fa-trash"></i></button>';
        }

        return[
            'id'                => $data->id,
            'code'              => $data->code,
            'name'              => $data->name,
            'status_id'         => $data->status_id,
            'status'            => $data->status->display,
            'action'            => $btn,
        ];
    }
}

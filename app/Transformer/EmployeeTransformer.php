<?php


namespace App\Transformer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class EmployeeTransformer extends TransformerAbstract
{
    public function transform($data){
        $date = Carbon::parse($data->created_at)->toIso8601String();

        $btn = '';
        if($data->status_id != 2){
            if(Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('employee.show')){
                $btn .= '<a class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" title="Show" href="'. route('employee.show', $data->id) .'"><i class="fa fa-fw fa-eye"></i></a>';
            }

            if(Auth::user()->is_superadmin || Auth::user()->hierarchy->hasPermissionTo('employee.edit')){
                $btn .= '&nbsp;<a class="btn btn-sm btn-secondary js-bs-tooltip-enabled" data-bs-toggle="tooltip" title="Edit" href="'. route('employee.edit', $data->id) .'"><i class="fa fa-fw fa-edit"></i></a>';
            }
//            $btn .= '<button class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled delete-btn" data-bs-toggle="tooltip" title="Delete" data-url="'. route('accounting.coa.delete', $row->id) .'"><i class="fa fa-fw fa-trash"></i></button>';
        }

        return[
//            'created_at'        => $date,
            'id'                => $data->id,
            'code'              => $data->code,
            'branch'            => $data->branch->name,
            'name'              => $data->name,
            // 'division'          => $data->division->name,
            'position'          => $data->employee_position->name,
            'action'            => $btn,
        ];
    }
}

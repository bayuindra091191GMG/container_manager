<?php

namespace App\Transformer;

use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class HierarchyTransformers extends TransformerAbstract
{
    public function transform($data)
    {

        $btn = '';
        if ($data->status_id != 2) {
            if (Auth::user()->isSuperAdmin()) {
                $btn .= '&nbsp;<a class="btn btn-sm btn-secondary js-bs-tooltip-enabled" data-bs-toggle="tooltip" title="Edit" href="'.route('level.edit', $data->id).'"><i class="fa fa-fw fa-edit"></i></a>';
            }
        }

        return [
            //            'created_at'        => $date,
            'id' => $data->id,
            'code' => $data->code,
            'name' => $data->name,
            'description' => $data->description,
            'created_at' => $data->created_at,
            'action' => $btn,
        ];
    }
}

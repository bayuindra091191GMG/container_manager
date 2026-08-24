<?php


namespace App\Transformer;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract   
{
    public function transform($data){
        $createdAt = Carbon::parse($data->created_at)->toIso8601String();

        $btn = '&nbsp;<a class="btn btn-sm btn-secondary" title="Edit" href="'. route('user.edit', $data->id) .'"><i class="fa fa-fw fa-edit"></i></a>';

        return[
            'id' => $data->id,
            'username' => $data->username,
            'level' => $data->level,
            'email' => $data->email,
            'status' => $data->status,
            'branch' => $data->branch,
            'created_at' => $createdAt,
            'action' => $btn,
        ];
    }
}

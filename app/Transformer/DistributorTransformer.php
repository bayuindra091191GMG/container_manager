<?php


namespace App\Transformer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class DistributorTransformer extends TransformerAbstract
{
    public function transform($recipient){
        $action = '<a class="btn btn-secondary btn-xs" href="'. route('distributor.edit', $recipient->id) .'"><i class="fa fa-fw fa-edit"></i></a>';
        $action .= '&nbsp;<button type="button" class="btn btn-danger btn-xs delete-btn" data-id="'. $recipient->id. '"><i class="fa fa-fw fa-trash"></i></button>';

        return[
            'id' => $recipient->id,
            'name' => $recipient->name,
            'code' => $recipient->code,
            'branch_name' => $recipient->branch_name,
            'action' => $action
        ];
    }
}

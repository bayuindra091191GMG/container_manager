<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShippingInstructionDetail
 *
 * @property int $id
 * @property int|null $branch_id
 * @property int|null $header_id
 * @property string|null $header_code
 * @property int|null $consignee_id
 * @property string|null $consignee_code
 * @property string|null $trucking
 * @property string|null $delivery_type
 * @property int|null $do_id
 * @property string|null $do_code
 * @property string|null $do_type
 * @property string|null $so_code
 * @property string|null $dr_code
 * @property string|null $shipto_code
 * @property string|null $shipto_name
 * @property int|null $item_id
 * @property string|null $item_code
 * @property string|null $item_name
 * @property float|null $qty
 * @property float|null $real_qty
 * @property string|null $unit
 * @property string|null $transportation
 * @property int|null $container1_id
 * @property string|null $container1_code
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $remark
 * @property ShippingInstructionHeader|null $shipping_instruction_header
 */
class ShippingInstructionDetail extends Model
{
    protected $table = 'shipping_instruction_details';

    protected $casts = [
        'branch_id' => 'int',
        'header_id' => 'int',
        'consignee_id' => 'int',
        'do_id' => 'int',
        'item_id' => 'int',
        'qty' => 'float',
        'real_qty' => 'float',
        'container1_id' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
    ];

    protected $fillable = [
        'branch_id',
        'header_id',
        'header_code',
        'consignee_id',
        'consignee_code',
        'trucking',
        'delivery_type',
        'do_id',
        'do_code',
        'do_type',
        'so_code',
        'dr_code',
        'shipto_code',
        'shipto_name',
        'item_id',
        'item_code',
        'item_name',
        'qty',
        'real_qty',
        'unit',
        'transportation',
        'container1_id',
        'container1_code',
        'created_by',
        'updated_by',
        'remark',
    ];

    public function shipping_instruction_header()
    {
        return $this->belongsTo(ShippingInstructionHeader::class, 'header_id');
    }
}

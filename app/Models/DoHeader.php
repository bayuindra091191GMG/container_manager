<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DoHeader
 *
 * @property int $id
 * @property int|null $branch_id
 * @property string|null $code
 * @property string|null $type
 * @property Carbon|null $date
 * @property string|null $so_code
 * @property string|null $dr_code
 * @property string|null $customer_code
 * @property string|null $customer_name
 * @property int|null $branch_destination_id
 * @property string|null $shipto_code
 * @property string|null $shipto_name
 * @property string|null $memo
 * @property string|null $shipping_company
 * @property string|null $do_pengalihan
 * @property int|null $si_id
 * @property string|null $si_code
 * @property bool|null $is_close
 * @property int|null $closed_by_si_id
 * @property int|null $container1_id
 * @property string|null $container1_code
 * @property string|null $transportation
 * @property int|null $status_id
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property Carbon|null $voided_at
 * @property Status|null $status
 * @property Collection|DoDetail[] $do_details
 * @property Collection|ShippingInstructionDetail[] $shipping_instruction_details
 */
class DoHeader extends Model
{
    protected $table = 'do_headers';

    protected $casts = [
        'branch_id' => 'int',
        'date' => 'datetime',
        'si_id' => 'int',
        'branch_destination_id' => 'int',
        'is_close' => 'bool',
        'closed_by_si_id' => 'int',
        'container1_id' => 'int',
        'status_id' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
    ];

    protected $fillable = [
        'branch_id',
        'code',
        'type',
        'date',
        'so_code',
        'dr_code',
        'customer_code',
        'customer_name',
        'branch_destination_id',
        'shipto_code',
        'shipto_name',
        'memo',
        'shipping_company',
        'do_pengalihan',
        'si_id',
        'si_code',
        'is_close',
        'closed_by_si_id',
        'container1_id',
        'container1_code',
        'transportation',
        'status_id',
        'created_by',
        'updated_by',
        'voided_at',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function do_details()
    {
        return $this->hasMany(DoDetail::class, 'header_id');
    }

    public function shipping_instruction_details()
    {
        return $this->hasMany(ShippingInstructionDetail::class, 'do_id');
    }
}

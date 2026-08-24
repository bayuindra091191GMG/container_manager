<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class ShippingInstructionHeader
 *
 * @property int $id
 * @property int|null $branch_id
 * @property string|null $code
 * @property Carbon|null $sailing_date
 * @property Carbon|null $docking_date
 * @property string|null $shipper
 * @property string|null $vessel_name
 * @property Carbon|null $eta_date
 * @property Carbon|null $etd_date
 * @property string|null $pic_receiver
 * @property string|null $port_receiver
 * @property int|null $province_id
 * @property string|null $destination
 * @property int|null $destination_branch_id
 * @property int|null $status_id
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property bool|null $is_sync
 * @property Carbon|null $dooring_at
 * @property Carbon|null $received_at
 * @property int|null $total_berthing_cont
 * @property int|null $total_dooring_cont
 * @property int|null $total_received_cont
 * @property Status|null $status
 * @property Collection|ShippingInstructionDetail[] $shipping_instruction_details
 * @property Collection|ShippingInstructionMonitor[] $shipping_instruction_monitors
 */
class ShippingInstructionHeader extends Model
{
    protected $table = 'shipping_instruction_headers';

    protected $casts = [
        'branch_id' => 'int',
        'sailing_date' => 'datetime',
        'docking_date' => 'datetime',
        'eta_date' => 'datetime',
        'etd_date' => 'datetime',
        'province_id' => 'int',
        'destination_branch_id' => 'int',
        'status_id' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'is_sync' => 'bool',
        'dooring_at' => 'datetime',
        'received_at' => 'datetime',
        'total_berthing_cont' => 'int',
        'total_dooring_cont' => 'int',
        'total_received_cont' => 'int',
    ];

    protected $fillable = [
        'branch_id',
        'code',
        'sailing_date',
        'docking_date',
        'shipper',
        'vessel_name',
        'eta_date',
        'etd_date',
        'pic_receiver',
        'port_receiver',
        'province_id',
        'destination',
        'destination_branch_id',
        'status_id',
        'created_by',
        'updated_by',
        'is_sync',
        'dooring_at',
        'received_at',
        'total_berthing_cont',
        'total_dooring_cont',
        'total_received_cont',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function shipping_instruction_details()
    {
        return $this->hasMany(ShippingInstructionDetail::class, 'header_id');
    }

    public function shipping_instruction_monitors()
    {
        return $this->hasMany(ShippingInstructionMonitor::class, 'header_id');
    }

    public static function recountContainerTotals($headerId)
    {
        $totals = DB::table('shipping_instruction_monitors')
            ->where('header_id', '=', $headerId)
            ->select(DB::raw('SUM(status_id = 9) as total_berthing_cont'),
                DB::raw('SUM(status_id = 13) as total_dooring_cont'),
                DB::raw('SUM(status_id = 18) as total_received_cont'))
            ->first();

        DB::table('shipping_instruction_headers')
            ->where('id', '=', $headerId)
            ->update([
                'total_berthing_cont' => $totals->total_berthing_cont ?? 0,
                'total_dooring_cont' => $totals->total_dooring_cont ?? 0,
                'total_received_cont' => $totals->total_received_cont ?? 0,
            ]);
    }
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContainerData
 *
 * @property int $id
 * @property int|null $branch_id
 * @property string|null $code
 * @property int|null $warehouse_id
 * @property int|null $do_id
 * @property string|null $do_code
 * @property string|null $do_type
 * @property int|null $timbangan_id
 * @property string|null $timbangan_code
 * @property int|null $status_id
 * @property int|null $warehouse_location_id
 * @property string|null $warehouse_location_status
 * @property string|null $warehouse_area
 * @property string|null $warehouse_area_row
 * @property int|null $warehouse_status_id
 * @property string|null $warehouse_status
 * @property string|null $warehouse_status_reason
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $si_code
 * @property int|null $si_destination_id
 * @property string|null $si_destination_name
 * @property Carbon|null $si_etd_date
 * @property Carbon|null $si_eta_date
 * @property Carbon|null $free_storage_start_at
 * @property Carbon|null $free_storage_end_at
 * @property Carbon|null $free_demurrage_start_at
 * @property Carbon|null $free_demurrage_end_at
 * @property Carbon|null $last_status_updated_at
 * @property Carbon|null $received_at
 * @property bool|null $is_over_storage
 * @property Carbon|null $returned_at
 * @property bool|null $is_over_demurrage
 * @property int|null $free_storage_duration
 * @property int|null $free_demurrage_duration
 * @property bool|null $is_storage_monitor_start
 * @property bool|null $is_storage_monitor_end
 * @property bool|null $is_demurrage_monitor_start
 * @property bool|null $is_demurrage_monitor_end
 * @property bool|null $is_sync
 * @property Carbon|null $dooring_at
 * @property Carbon|null $berthing_at
 * @property string|null $vessel_name
 * @property Carbon|null $semen_out_at
 * @property Carbon|null $ceramic_received_at
 * @property Carbon|null $ceramic_out_at
 *
 * @property Collection|ContainerBrokenImage[] $container_broken_images
 *
 * @package App\Models
 */
class ContainerData extends Model
{
	protected $table = 'container_datas';

	protected $casts = [
		'branch_id' => 'int',
		'warehouse_id' => 'int',
		'do_id' => 'int',
		'timbangan_id' => 'int',
		'status_id' => 'int',
		'warehouse_location_id' => 'int',
		'warehouse_status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
		'si_destination_id' => 'int',
		'si_etd_date' => 'datetime',
		'si_eta_date' => 'datetime',
		'free_storage_start_at' => 'datetime',
		'free_storage_end_at' => 'datetime',
		'free_demurrage_start_at' => 'datetime',
		'free_demurrage_end_at' => 'datetime',
		'last_status_updated_at' => 'datetime',
		'received_at' => 'datetime',
		'is_over_storage' => 'bool',
		'returned_at' => 'datetime',
		'is_over_demurrage' => 'bool',
		'free_storage_duration' => 'int',
		'free_demurrage_duration' => 'int',
		'is_storage_monitor_start' => 'bool',
		'is_storage_monitor_end' => 'bool',
		'is_demurrage_monitor_start' => 'bool',
		'is_demurrage_monitor_end' => 'bool',
        'is_pending_sync' => 'bool',
        'berthing_at' => 'datetime'
	];

	protected $fillable = [
		'branch_id',
		'code',
		'warehouse_id',
		'do_id',
		'do_code',
        'do_type',
		'timbangan_id',
		'timbangan_code',
        'timbangan_ceramic_code',
		'status_id',
		'warehouse_location_id',
		'warehouse_location_status',
		'warehouse_area',
		'warehouse_area_row',
		'warehouse_status_id',
		'warehouse_status',
		'warehouse_status_reason',
		'created_by',
		'updated_by',
		'si_code',
		'si_destination_id',
		'si_destination_name',
		'si_etd_date',
		'si_eta_date',
		'free_storage_start_at',
		'free_storage_end_at',
		'free_demurrage_start_at',
		'free_demurrage_end_at',
		'last_status_updated_at',
		'received_at',
		'is_over_storage',
		'returned_at',
		'is_over_demurrage',
		'free_storage_duration',
		'free_demurrage_duration',
		'is_storage_monitor_start',
		'is_storage_monitor_end',
		'is_demurrage_monitor_start',
		'is_demurrage_monitor_end',
        'is_sync',
        'dooring_at',
        'item_name',
        'berthing_at',
        'vessel_name',
        'semen_out_at',
        'ceramic_received_at',
        'ceramic_out_at'
	];

	public function container_broken_images()
	{
		return $this->hasMany(ContainerBrokenImage::class, 'container_id');
	}
}

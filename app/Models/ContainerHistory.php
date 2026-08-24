<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContainerHistory
 *
 * @property int $id
 * @property string|null $code
 * @property int|null $do_id
 * @property string|null $do_code
 * @property string|null $do_type
 * @property string|null $item_name
 * @property string|null $vessel_name
 * @property int|null $timbangan_id
 * @property string|null $timbangan_code
 * @property string|null $timbangan_ceramic_code
 * @property string|null $si_code
 * @property int|null $si_destination_id
 * @property string|null $si_destination_name
 * @property Carbon|null $si_etd_date
 * @property Carbon|null $si_eta_date
 * @property int|null $free_storage_duration
 * @property int|null $free_demurrage_duration
 * @property Carbon|null $free_storage_start_at
 * @property Carbon|null $free_storage_end_at
 * @property Carbon|null $free_demurrage_start_at
 * @property Carbon|null $free_demurrage_end_at
 * @property bool|null $is_over_storage
 * @property bool|null $is_over_demurrage
 * @property Carbon|null $berthing_at
 * @property Carbon|null $dooring_at
 * @property Carbon|null $received_at
 * @property Carbon|null $returned_at
 * @property Carbon|null $semen_out_at
 * @property Carbon|null $ceramic_received_at
 * @property Carbon|null $ceramic_out_at
 * @property int|null $days_since_berthing
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ContainerHistory extends Model
{
	protected $table = 'container_histories';

	protected $casts = [
		'do_id' => 'int',
		'timbangan_id' => 'int',
		'si_destination_id' => 'int',
		'si_etd_date' => 'datetime',
		'si_eta_date' => 'datetime',
		'free_storage_duration' => 'int',
		'free_demurrage_duration' => 'int',
		'free_storage_start_at' => 'datetime',
		'free_storage_end_at' => 'datetime',
		'free_demurrage_start_at' => 'datetime',
		'free_demurrage_end_at' => 'datetime',
		'is_over_storage' => 'bool',
		'is_over_demurrage' => 'bool',
		'berthing_at' => 'datetime',
		'dooring_at' => 'datetime',
		'received_at' => 'datetime',
		'returned_at' => 'datetime',
		'semen_out_at' => 'datetime',
		'ceramic_received_at' => 'datetime',
		'ceramic_out_at' => 'datetime',
        'days_since_berthing' => 'int'
	];

	protected $fillable = [
		'code',
		'do_id',
		'do_code',
		'do_type',
		'item_name',
		'vessel_name',
		'timbangan_id',
		'timbangan_code',
		'timbangan_ceramic_code',
		'si_code',
		'si_destination_id',
		'si_destination_name',
		'si_etd_date',
		'si_eta_date',
		'free_storage_duration',
		'free_demurrage_duration',
		'free_storage_start_at',
		'free_storage_end_at',
		'free_demurrage_start_at',
		'free_demurrage_end_at',
		'is_over_storage',
		'is_over_demurrage',
		'berthing_at',
		'dooring_at',
		'received_at',
		'returned_at',
		'semen_out_at',
		'ceramic_received_at',
		'ceramic_out_at',
        'days_since_berthing'
	];
}

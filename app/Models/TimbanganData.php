<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TimbanganData
 * 
 * @property int $id
 * @property int|null $branch_id
 * @property string|null $code
 * @property string|null $container_no
 * @property string|null $weight_type
 * @property float|null $weight_1
 * @property Carbon|null $weight_1_datetime
 * @property float|null $weight_2
 * @property Carbon|null $weight_2_datetime
 * @property float|null $berat_bruto
 * @property float|null $berat_tarra
 * @property float|null $berat_netto
 * @property string|null $plat_no
 * @property string|null $trado_no
 * @property string|null $driver_name
 * @property string|null $out_destination
 * @property int|null $status_id
 * @property string|null $customer_code
 * @property string|null $customer_name
 * @property string|null $expedition
 * @property string|null $item_name
 * @property string|null $pos_in
 * @property string|null $pos_out
 * @property string|null $description
 * @property string|null $udf_01
 * @property string|null $udf_02
 * @property string|null $udf_03
 * @property string|null $udf_04
 * @property string|null $udf_05
 * @property bool|null $is_sync
 * @property int|null $is_printed
 * @property int|null $print_counter
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property int|null $is_approve
 * @property int|null $approve_by
 * @property Carbon|null $approve_at
 *
 * @package App\Models
 */
class TimbanganData extends Model
{
	protected $table = 'timbangan_datas';

	protected $casts = [
		'branch_id' => 'int',
		'weight_1' => 'float',
		'weight_1_datetime' => 'datetime',
		'weight_2' => 'float',
		'weight_2_datetime' => 'datetime',
		'berat_bruto' => 'float',
		'berat_tarra' => 'float',
		'berat_netto' => 'float',
		'status_id' => 'int',
		'is_sync' => 'bool',
		'is_printed' => 'int',
		'print_counter' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
		'is_approve' => 'int',
		'approve_by' => 'int',
		'approve_at' => 'datetime'
	];

	protected $fillable = [
		'branch_id',
		'code',
		'container_no',
		'weight_type',
		'weight_1',
		'weight_1_datetime',
		'weight_2',
		'weight_2_datetime',
		'berat_bruto',
		'berat_tarra',
		'berat_netto',
		'plat_no',
		'trado_no',
		'driver_name',
		'out_destination',
		'status_id',
		'customer_code',
		'customer_name',
		'expedition',
		'item_name',
		'pos_in',
		'pos_out',
		'description',
		'udf_01',
		'udf_02',
		'udf_03',
		'udf_04',
		'udf_05',
		'is_sync',
		'is_printed',
		'print_counter',
		'created_by',
		'updated_by',
		'is_approve',
		'approve_by',
		'approve_at'
	];
}

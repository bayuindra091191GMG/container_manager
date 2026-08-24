<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContainerLog
 *
 * @property int $id
 * @property int|null $branch_id
 * @property int|null $container_id
 * @property int|null $reference_id
 * @property string|null $reference_code
 * @property string|null $reference_type
 * @property string|null $transaction_description
 * @property Carbon|null $transaction_date
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 *
 * @package App\Models
 */
class ContainerLog extends Model
{
	protected $table = 'container_logs';

	protected $casts = [
		'id' => 'int',
		'branch_id' => 'int',
		'container_id' => 'int',
		'reference_id' => 'int',
		'transaction_date' => 'datetime',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'branch_id',
		'container_id',
		'reference_id',
		'reference_code',
		'reference_type',
		'transaction_description',
		'transaction_date',
		'created_by',
		'updated_by'
	];
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DoDetail
 *
 * @property int $id
 * @property int|null $header_id
 * @property string|null $transfer_no
 * @property int|null $item_id
 * @property string|null $item_code
 * @property string|null $item_name
 * @property float|null $qty
 * @property string|null $unit
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property DoHeader|null $do_header
 *
 * @package App\Models
 */
class DoDetail extends Model
{
	protected $table = 'do_details';

	protected $casts = [
		'header_id' => 'int',
		'qty' => 'float'
	];

	protected $fillable = [
		'header_id',
		'transfer_no',
		'item_id',
		'item_code',
		'item_name',
		'qty',
		'unit'
	];

	public function do_header()
	{
		return $this->belongsTo(DoHeader::class, 'header_id');
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShippingInstructionUpload
 * 
 * @property int $id
 * @property int|null $header_id
 * @property string|null $filename
 * @property string|null $ext
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ShippingInstructionHeader|null $shipping_instruction_header
 *
 * @package App\Models
 */
class ShippingInstructionUpload extends Model
{
	protected $table = 'shipping_instruction_uploads';

	protected $casts = [
		'header_id' => 'int'
	];

	protected $fillable = [
		'header_id',
		'filename',
		'ext'
	];

	public function shipping_instruction_header()
	{
		return $this->belongsTo(ShippingInstructionHeader::class, 'header_id');
	}
}

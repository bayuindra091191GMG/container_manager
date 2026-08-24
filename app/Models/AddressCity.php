<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AddressCity
 * 
 * @property int $id
 * @property string|null $code
 * @property int|null $province_id
 * @property string|null $type
 * @property string|null $name
 * @property string|null $postal_code
 *
 * @package App\Models
 */
class AddressCity extends Model
{
	protected $table = 'address_cities';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'province_id' => 'int'
	];

	protected $fillable = [
		'code',
		'province_id',
		'type',
		'name',
		'postal_code'
	];
}

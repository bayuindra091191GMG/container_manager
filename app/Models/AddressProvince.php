<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AddressProvince
 * 
 * @property int $id
 * @property string|null $code
 * @property string|null $name
 *
 * @package App\Models
 */
class AddressProvince extends Model
{
	protected $table = 'address_provinces';
	public $timestamps = false;

	protected $fillable = [
		'code',
		'name'
	];
}

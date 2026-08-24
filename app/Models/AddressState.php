<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AddressState
 * 
 * @property int $id
 * @property int $country_id
 * @property string $code
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property AddressCountry $address_country
 *
 * @package App\Models
 */
class AddressState extends Model
{
	protected $table = 'address_states';

	protected $casts = [
		'country_id' => 'int'
	];

	protected $fillable = [
		'country_id',
		'code',
		'name'
	];

	public function address_country()
	{
		return $this->belongsTo(AddressCountry::class, 'country_id');
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Customer
 * 
 * @property int $id
 * @property string|null $guid
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $email
 * @property int|null $is_from_app
 * @property Carbon|null $birthdate
 * @property int|null $age
 * @property string|null $sex
 * @property string|null $marital_status
 * @property string|null $img_path
 * @property string|null $address
 * @property string|null $ktp
 * @property string|null $identity_address
 * @property int|null $status_id
 * @property int|null $is_client
 * @property string|null $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $forgot_password_otp_code
 * @property Carbon|null $forgot_password_otp_expired_at
 * @property string|null $forgot_password_unique_code
 * @property string|null $otp_code
 * @property string|null $pin
 * @property int|null $is_thirdparty_auth
 * @property string|null $thirdparty_name
 * 
 * @property Status|null $status
 *
 * @package App\Models
 */
class Customer extends Model
{
	protected $table = 'customers';

	protected $casts = [
		'is_from_app' => 'int',
		'birthdate' => 'datetime',
		'age' => 'int',
		'status_id' => 'int',
		'is_client' => 'int',
		'forgot_password_otp_expired_at' => 'datetime',
		'is_thirdparty_auth' => 'int'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'guid',
		'name',
		'phone',
		'email',
		'is_from_app',
		'birthdate',
		'age',
		'sex',
		'marital_status',
		'img_path',
		'address',
		'ktp',
		'identity_address',
		'status_id',
		'is_client',
		'password',
		'forgot_password_otp_code',
		'forgot_password_otp_expired_at',
		'forgot_password_unique_code',
		'otp_code',
		'pin',
		'is_thirdparty_auth',
		'thirdparty_name'
	];

	public function status()
	{
		return $this->belongsTo(Status::class);
	}
}

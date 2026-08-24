<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApiLog
 * 
 * @property int $id
 * @property string|null $ip_address
 * @property string|null $json_request
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ApiLog extends Model
{
	protected $table = 'api_logs';

	protected $fillable = [
		'ip_address',
		'json_request'
	];
}

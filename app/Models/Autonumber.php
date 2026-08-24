<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Autonumber
 * 
 * @property int $id
 * @property string|null $doc_code
 * @property int|null $next_no
 * @property int|null $current_month
 * @property int|null $current_year
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Autonumber extends Model
{
	protected $table = 'autonumbers';

	protected $casts = [
		'next_no' => 'int',
		'current_month' => 'int',
		'current_year' => 'int'
	];

	protected $fillable = [
		'doc_code',
		'next_no',
		'current_month',
		'current_year'
	];
}

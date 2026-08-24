<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Menu
 * 
 * @property int $id
 * @property int|null $sequence
 * @property string|null $code
 * @property string|null $name
 * @property string|null $actions
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Menu extends Model
{
	protected $table = 'menus';

	protected $casts = [
		'sequence' => 'int'
	];

	protected $fillable = [
		'sequence',
		'code',
		'name',
		'actions'
	];
}

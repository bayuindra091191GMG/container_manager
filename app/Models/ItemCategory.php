<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ItemCategory
 * 
 * @property int $id
 * @property int|null $branch_id
 * @property string|null $code
 * @property string|null $name
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $status_id
 * 
 * @property Branch|null $branch
 *
 * @package App\Models
 */
class ItemCategory extends Model
{
	protected $table = 'item_categories';

	protected $casts = [
		'branch_id' => 'int',
		'created_by' => 'int',
		'status_id' => 'int'
	];

	protected $fillable = [
		'branch_id',
		'code',
		'name',
		'created_by',
		'status_id'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}
}

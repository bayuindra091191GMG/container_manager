<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Division
 * 
 * @property int $id
 * @property string|null $code
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property int|null $branch_id
 * @property int|null $status_id
 * 
 * @property Branch|null $branch
 * @property User|null $user
 * @property Status|null $status
 * @property Collection|Employee[] $employees
 *
 * @package App\Models
 */
class Division extends Model
{
	protected $table = 'divisions';

	protected $casts = [
		'created_by' => 'int',
		'updated_by' => 'int',
		'branch_id' => 'int',
		'status_id' => 'int'
	];

	protected $fillable = [
		'code',
		'name',
		'created_by',
		'updated_by',
		'branch_id',
		'status_id'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	public function employees()
	{
		return $this->hasMany(Employee::class);
	}
}

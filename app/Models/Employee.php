<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Employee
 * 
 * @property int $id
 * @property int|null $branch_id
 * @property string|null $code
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property int|null $division_id
 * @property int|null $hierarchy_id
 * @property int|null $position_id
 * @property string|null $npwp
 * @property string|null $id_card_no
 * @property Carbon|null $join_date
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property int|null $status_id
 * @property string|null $guid
 * 
 * @property Branch|null $branch
 * @property User|null $user
 * @property Division|null $division
 * @property EmployeePosition|null $employee_position
 * @property Status|null $status
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Employee extends Model
{
	protected $table = 'employees';

	protected $casts = [
		'branch_id' => 'int',
		'division_id' => 'int',
		'hierarchy_id' => 'int',
		'position_id' => 'int',
		'join_date' => 'datetime',
		'created_by' => 'int',
		'updated_by' => 'int',
		'status_id' => 'int'
	];

	protected $fillable = [
		'branch_id',
		'code',
		'name',
		'phone',
		'email',
		'address',
		'division_id',
		'hierarchy_id',
		'position_id',
		'npwp',
		'id_card_no',
		'join_date',
		'created_by',
		'updated_by',
		'status_id',
		'guid'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	public function division()
	{
		return $this->belongsTo(Division::class);
	}

	public function employee_position()
	{
		return $this->belongsTo(EmployeePosition::class, 'position_id');
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	public function users()
	{
		return $this->hasMany(User::class);
	}
}

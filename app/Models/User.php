<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @property int $id
 * @property int|null $hierarchy_id
 * @property string|null $username
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $employee_id
 * @property int|null $is_superadmin
 * @property int|null $branch_id
 * @property int|null $status_id
 * @property string|null $forgot_password_otp_code
 * @property Carbon|null $forgot_password_otp_expired_at
 * @property string|null $forgot_password_unique_code
 * @property string|null $otp_code
 * @property int|null $sampling_station_id
 *
 * @property Branch|null $branch
 * @property Employee|null $employee
 * @property Hierarchy|null $hierarchy
 * @property Collection|Branch[] $branches
 * @property Collection|Consignee[] $consignees
 * @property Collection|Division[] $divisions
 * @property Collection|Employee[] $employees
 * @property Collection|Item[] $items
 * @property Collection|UnitMaterial[] $unit_materials
 * @property Collection|Warehouse[] $warehouses
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
	protected $table = 'users';

	protected $casts = [
		'hierarchy_id' => 'int',
		'email_verified_at' => 'datetime',
		'employee_id' => 'int',
		'is_superadmin' => 'int',
		'branch_id' => 'int',
		'status_id' => 'int',
		'forgot_password_otp_expired_at' => 'datetime',
		'sampling_station_id' => 'int'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'hierarchy_id',
		'username',
		'name',
		'email',
		'phone',
		'email_verified_at',
		'password',
		'remember_token',
		'employee_id',
		'is_superadmin',
		'branch_id',
		'status_id',
		'forgot_password_otp_code',
		'forgot_password_otp_expired_at',
		'forgot_password_unique_code',
		'otp_code',
		'sampling_station_id'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}

	public function hierarchy()
	{
		return $this->belongsTo(Hierarchy::class);
	}

	public function branches()
	{
		return $this->belongsToMany(Branch::class, 'user_branches')
					->withPivot('id')
					->withTimestamps();
	}

	public function consignees()
	{
		return $this->hasMany(Consignee::class, 'updated_by');
	}

	public function divisions()
	{
		return $this->hasMany(Division::class, 'updated_by');
	}

	public function employees()
	{
		return $this->hasMany(Employee::class, 'updated_by');
	}

	public function items()
	{
		return $this->hasMany(Item::class, 'updated_by');
	}

	public function unit_materials()
	{
		return $this->hasMany(UnitMaterial::class, 'updated_by');
	}

	public function warehouses()
	{
		return $this->hasMany(Warehouse::class, 'updated_by');
	}
}

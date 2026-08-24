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
 * @property int|null $branch_id
 * @property int|null $status_id
 * @property string|null $forgot_password_otp_code
 * @property Carbon|null $forgot_password_otp_expired_at
 * @property string|null $forgot_password_unique_code
 * @property string|null $otp_code
 * @property int|null $sampling_station_id
 * @property Branch|null $branch
 * @property Employee|null $employee
 * @property Collection|Branch[] $branches
 * @property Collection|Consignee[] $consignees
 * @property Collection|Division[] $divisions
 * @property Collection|Employee[] $employees
 * @property Collection|Item[] $items
 * @property Collection|UnitMaterial[] $unit_materials
 * @property Collection|Warehouse[] $warehouses
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $table = 'users';

    protected $casts = [
        'email_verified_at' => 'datetime',
        'employee_id' => 'int',
        'branch_id' => 'int',
        'status_id' => 'int',
        'forgot_password_otp_expired_at' => 'datetime',
        'sampling_station_id' => 'int',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'username',
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'remember_token',
        'employee_id',
        'branch_id',
        'status_id',
        'forgot_password_otp_code',
        'forgot_password_otp_expired_at',
        'forgot_password_unique_code',
        'otp_code',
        'sampling_station_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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

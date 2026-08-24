<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Branch
 * 
 * @property int $id
 * @property string|null $code
 * @property string|null $name
 * @property string|null $address
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $parent_id
 * @property string|null $postal_code
 * @property int|null $country_id
 * @property string|null $country_name
 * @property int|null $province_id
 * @property string|null $province_name
 * @property int|null $city_id
 * @property string|null $city_name
 * @property int|null $district_id
 * @property string|null $district_name
 * @property int|null $status_id
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property int|null $free_storage_duration
 * @property int|null $free_demurrage_duration
 * 
 * @property Status|null $status
 * @property User|null $user
 * @property Collection|Consignee[] $consignees
 * @property Collection|Division[] $divisions
 * @property Collection|Employee[] $employees
 * @property Collection|ItemCategory[] $item_categories
 * @property Collection|Item[] $items
 * @property Collection|UnitMaterial[] $unit_materials
 * @property Collection|User[] $users
 * @property Collection|Warehouse[] $warehouses
 *
 * @package App\Models
 */
class Branch extends Model
{
	protected $table = 'branches';

	protected $casts = [
		'latitude' => 'float',
		'longitude' => 'float',
		'parent_id' => 'int',
		'country_id' => 'int',
		'province_id' => 'int',
		'city_id' => 'int',
		'district_id' => 'int',
		'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
		'free_storage_duration' => 'int',
		'free_demurrage_duration' => 'int'
	];

	protected $fillable = [
		'code',
		'name',
		'address',
		'latitude',
		'longitude',
		'parent_id',
		'postal_code',
		'country_id',
		'country_name',
		'province_id',
		'province_name',
		'city_id',
		'city_name',
		'district_id',
		'district_name',
		'status_id',
		'created_by',
		'updated_by',
		'free_storage_duration',
		'free_demurrage_duration'
	];

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	public function consignees()
	{
		return $this->hasMany(Consignee::class);
	}

	public function divisions()
	{
		return $this->hasMany(Division::class);
	}

	public function employees()
	{
		return $this->hasMany(Employee::class);
	}

	public function item_categories()
	{
		return $this->hasMany(ItemCategory::class);
	}

	public function items()
	{
		return $this->hasMany(Item::class);
	}

	public function unit_materials()
	{
		return $this->hasMany(UnitMaterial::class);
	}

	public function users()
	{
		return $this->hasMany(User::class);
	}

	public function warehouses()
	{
		return $this->hasMany(Warehouse::class);
	}
}

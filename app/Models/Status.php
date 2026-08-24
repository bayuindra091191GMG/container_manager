<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Status
 * 
 * @property int $id
 * @property string|null $description
 * @property string|null $display
 * 
 * @property Collection|Branch[] $branches
 * @property Collection|Customer[] $customers
 * @property Collection|Division[] $divisions
 * @property Collection|DoHeader[] $do_headers
 * @property Collection|Employee[] $employees
 * @property Collection|Item[] $items
 * @property Collection|ShippingInstructionHeader[] $shipping_instruction_headers
 * @property Collection|UnitMaterial[] $unit_materials
 *
 * @package App\Models
 */
class Status extends Model
{
	protected $table = 'statuses';
	public $timestamps = false;

	protected $fillable = [
		'description',
		'display'
	];

	public function branches()
	{
		return $this->hasMany(Branch::class);
	}

	public function customers()
	{
		return $this->hasMany(Customer::class);
	}

	public function divisions()
	{
		return $this->hasMany(Division::class);
	}

	public function do_headers()
	{
		return $this->hasMany(DoHeader::class);
	}

	public function employees()
	{
		return $this->hasMany(Employee::class);
	}

	public function items()
	{
		return $this->hasMany(Item::class);
	}

	public function shipping_instruction_headers()
	{
		return $this->hasMany(ShippingInstructionHeader::class);
	}

	public function unit_materials()
	{
		return $this->hasMany(UnitMaterial::class);
	}
}

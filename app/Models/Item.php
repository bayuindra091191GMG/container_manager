<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Item
 * 
 * @property int $id
 * @property string|null $guid
 * @property int|null $branch_id
 * @property string|null $barcode
 * @property string|null $code
 * @property string|null $name
 * @property string|null $image_path
 * @property int|null $have_bom
 * @property int|null $category_id
 * @property int|null $category_2_id
 * @property int|null $status_id
 * @property float|null $price
 * @property string|null $description
 * @property string|null $img_path
 * @property int|null $is_unlimited_stock
 * @property int|null $capacity
 * @property float|null $cost_price
 * @property int|null $default_uom_id
 * @property string|null $type
 * @property int|null $max_capacity
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property float|null $discounted_price
 * @property float|null $discount
 * @property float|null $rating
 * @property string|null $total_qty_sold
 * @property float|null $sku_height
 * @property float|null $sku_length
 * @property float|null $sku_width
 * @property float|null $gross_weight
 * @property float|null $net_weight
 * 
 * @property Branch|null $branch
 * @property User|null $user
 * @property Status|null $status
 * @property Collection|ItemUnit[] $item_units
 *
 * @package App\Models
 */
class Item extends Model
{
	protected $table = 'items';

	protected $casts = [
		'branch_id' => 'int',
		'have_bom' => 'int',
		'category_id' => 'int',
		'category_2_id' => 'int',
		'status_id' => 'int',
		'price' => 'float',
		'is_unlimited_stock' => 'int',
		'capacity' => 'int',
		'cost_price' => 'float',
		'default_uom_id' => 'int',
		'max_capacity' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
		'discounted_price' => 'float',
		'discount' => 'float',
		'rating' => 'float',
		'sku_height' => 'float',
		'sku_length' => 'float',
		'sku_width' => 'float',
		'gross_weight' => 'float',
		'net_weight' => 'float'
	];

	protected $fillable = [
		'guid',
		'branch_id',
		'barcode',
		'code',
		'name',
		'image_path',
		'have_bom',
		'category_id',
		'category_2_id',
		'status_id',
		'price',
		'description',
		'img_path',
		'is_unlimited_stock',
		'capacity',
		'cost_price',
		'default_uom_id',
		'type',
		'max_capacity',
		'created_by',
		'updated_by',
		'discounted_price',
		'discount',
		'rating',
		'total_qty_sold',
		'sku_height',
		'sku_length',
		'sku_width',
		'gross_weight',
		'net_weight'
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

	public function item_units()
	{
		return $this->hasMany(ItemUnit::class);
	}
}

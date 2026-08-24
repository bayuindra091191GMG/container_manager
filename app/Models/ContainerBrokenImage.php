<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContainerBrokenImage
 * 
 * @property int $id
 * @property int|null $branch_id
 * @property int|null $container_id
 * @property string|null $filename
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * 
 * @property ContainerData|null $container_data
 *
 * @package App\Models
 */
class ContainerBrokenImage extends Model
{
	protected $table = 'container_broken_images';

	protected $casts = [
		'branch_id' => 'int',
		'container_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'branch_id',
		'container_id',
		'filename',
		'created_by',
		'updated_by'
	];

	public function container_data()
	{
		return $this->belongsTo(ContainerData::class, 'container_id');
	}
}

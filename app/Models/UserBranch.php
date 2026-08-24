<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserBranch
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $branch_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Branch|null $branch
 * @property User|null $user
 *
 * @package App\Models
 */
class UserBranch extends Model
{
	protected $table = 'user_branches';

	protected $casts = [
		'user_id' => 'int',
		'branch_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'branch_id'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}

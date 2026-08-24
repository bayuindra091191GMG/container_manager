<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Hierarchy
 *
 * @property int $id
 * @property int|null $branch_id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Hierarchy extends Model
{
    use HasRoles, HasPermissions;

    protected $guard_name = 'web';

	protected $table = 'hierarchies';

	protected $casts = [
		'branch_id' => 'int'
	];

	protected $fillable = [
		'branch_id',
		'name',
		'code',
		'description'
	];
}

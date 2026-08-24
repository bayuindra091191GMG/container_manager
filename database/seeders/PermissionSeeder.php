<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        'menu_access',
        'level.list',
        'level.add',
        'level.edit',
        'user.list',
        'user.add',
        'user.edit',
        'branch.list',
        'branch.add',
        'branch.show',
        'branch.edit',
        'consignee.list',
        'consignee.add',
        'consignee.show',
        'consignee.edit',
        'consignee.delete',
        'distributor.list',
        'distributor.add',
        'distributor.delete',
        'division.list',
        'division.add',
        'division.show',
        'division.edit',
        'employee.list',
        'employee.add',
        'employee.show',
        'employee.edit',
        'job_position.list',
        'job_position.add',
        'job_position.show',
        'job_position.edit',
        'hierarchy.list',
        'hierarchy.add',
        'hierarchy.show',
        'warehouse.list',
        'warehouse.add',
        'warehouse.show',
        'warehouse.edit',
        'warehouse.delete',
        'container_history.list',
        'do.list',
        'do.add',
        'do.show',
        'do.edit',
        'do.delete',
        'si.list',
        'si.add',
        'si.show',
        'si.edit-si',
        'si.edit-cabang',
        'si.print',
        'si.delete',
        'si.document-download',
        'si.document-upload',
        'si.document-delete',
        'container.list',
        'warehouse_management.list',
        'warehouse_management.show',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(self::PERMISSIONS)
            ->map(fn (string $name) => Permission::findOrCreate($name, 'web'));

        $superAdmin = Role::findOrCreate('super-admin', 'web');
        Role::findOrCreate('staff', 'web');
        $superAdmin->syncPermissions($permissions);

        User::where('username', 'developer')
            ->firstOrFail()
            ->syncRoles([$superAdmin]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DirectSpatieAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null,
        ]);

        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('username');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->unique(['name', 'guard_name']);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->unique(['name', 'guard_name']);
            $table->timestamps();
        });

        Schema::create('model_has_roles', function (Blueprint $table): void {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table): void {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table): void {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_role_permission_is_checked_directly_on_user(): void
    {
        $user = User::create([
            'username' => 'operator',
            'name' => 'Operator',
            'email' => 'operator@example.test',
            'password' => 'secret',
        ]);
        $role = Role::create(['name' => 'operator', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'do.list', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('operator'));
        $this->assertTrue($user->can('do.list'));
        $this->assertFalse($user->can('si.list'));
    }

    public function test_super_admin_role_bypasses_permission_middleware(): void
    {
        $user = User::create([
            'username' => 'developer',
            'name' => 'Developer',
            'email' => 'developer@example.test',
            'password' => 'secret',
        ]);
        $user->assignRole(Role::create(['name' => 'super-admin', 'guard_name' => 'web']));

        Route::middleware(['auth', 'permission:si.list'])
            ->get('/direct-spatie-test', fn () => response('ok'));

        $this->actingAs($user)
            ->get('/direct-spatie-test')
            ->assertOk()
            ->assertSeeText('ok');
    }
}

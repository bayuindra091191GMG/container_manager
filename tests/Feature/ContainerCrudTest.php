<?php

namespace Tests\Feature;

use App\Http\Controllers\ContainerManagementController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ContainerCrudTest extends TestCase
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

        $schema = Schema::connection('sqlite');
        $schema->create('branches', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });
        $schema->create('statuses', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('display');
        });
        $schema->create('container_datas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('do_id')->nullable();
            $table->string('do_code')->nullable();
            $table->unsignedBigInteger('timbangan_id')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->unsignedInteger('warehouse_location_id')->nullable();
            $table->string('warehouse_location_status')->nullable();
            $table->string('warehouse_area')->nullable();
            $table->string('warehouse_area_row')->nullable();
            $table->unsignedInteger('warehouse_status_id')->nullable();
            $table->string('warehouse_status')->nullable();
            $table->string('si_code')->nullable();
            $table->unsignedBigInteger('si_destination_id')->nullable();
            $table->string('si_destination_name')->nullable();
            $table->boolean('is_over_storage')->default(false);
            $table->boolean('is_over_demurrage')->default(false);
            $table->boolean('is_storage_monitor_start')->default(false);
            $table->boolean('is_storage_monitor_end')->default(false);
            $table->boolean('is_demurrage_monitor_start')->default(false);
            $table->boolean('is_demurrage_monitor_end')->default(false);
            $table->boolean('is_sync')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
        $schema->create('shipping_instruction_monitors', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('container_id')->nullable();
        });
        $schema->create('container_histories', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->nullable();
        });

        DB::table('branches')->insert([
            ['id' => 1, 'name' => 'Karawang'],
            ['id' => 2, 'name' => 'Medan'],
        ]);
        DB::table('statuses')->insert([
            ['id' => 2, 'display' => 'INACTIVE'],
            ['id' => 7, 'display' => 'PENDING'],
        ]);

        $user = new User;
        $user->forceFill(['id' => 1]);
        $user->exists = true;
        $this->createSuperAdminRole();
        $this->actingAs($user);
    }

    public function test_container_can_be_created_as_pending(): void
    {
        $response = app(ContainerManagementController::class)->store(Request::create(
            '/container/store',
            'POST',
            ['code' => 'cont-new', 'branch_id' => 1]
        ));

        $container = DB::table('container_datas')->where('code', 'CONT-NEW')->first();

        $this->assertSame(route('container.show', $container->id), $response->getTargetUrl());
        $this->assertSame(1, $container->branch_id);
        $this->assertSame(7, $container->status_id);
        $this->assertNull($container->do_id);
        $this->assertNull($container->si_code);
    }

    public function test_duplicate_container_code_is_rejected(): void
    {
        app(ContainerManagementController::class)->store(Request::create(
            '/container/store',
            'POST',
            ['code' => 'CONT-DUP', 'branch_id' => 1]
        ));

        $this->expectException(ValidationException::class);
        app(ContainerManagementController::class)->store(Request::create(
            '/container/store',
            'POST',
            ['code' => 'CONT-DUP', 'branch_id' => 1]
        ));
    }

    public function test_pending_container_can_be_updated_and_soft_deleted(): void
    {
        app(ContainerManagementController::class)->store(Request::create(
            '/container/store',
            'POST',
            ['code' => 'CONT-EDIT', 'branch_id' => 1]
        ));
        $id = DB::table('container_datas')->where('code', 'CONT-EDIT')->value('id');

        app(ContainerManagementController::class)->update(Request::create(
            '/container/update',
            'POST',
            ['id' => $id, 'code' => 'CONT-UPDATED', 'branch_id' => 2]
        ));

        $updated = DB::table('container_datas')->find($id);
        $this->assertSame('CONT-UPDATED', $updated->code);
        $this->assertSame(2, $updated->branch_id);

        app(ContainerManagementController::class)->delete(Request::create(
            '/container/delete',
            'POST',
            ['id' => $id]
        ));

        $this->assertSame(2, DB::table('container_datas')->find($id)->status_id);
    }

    public function test_linked_container_cannot_be_edited_or_deleted(): void
    {
        app(ContainerManagementController::class)->store(Request::create(
            '/container/store',
            'POST',
            ['code' => 'CONT-LINKED', 'branch_id' => 1]
        ));
        $id = DB::table('container_datas')->where('code', 'CONT-LINKED')->value('id');
        DB::table('container_datas')->where('id', $id)->update([
            'do_id' => 99,
            'si_code' => 'SI-99',
        ]);

        $editResponse = app(ContainerManagementController::class)->edit($id);
        $deleteResponse = app(ContainerManagementController::class)->delete(Request::create(
            '/container/delete',
            'POST',
            ['id' => $id]
        ));

        $this->assertSame(route('container.show', $id), $editResponse->getTargetUrl());
        $this->assertSame(route('container.show', $id), $deleteResponse->getTargetUrl());
        $this->assertSame(7, DB::table('container_datas')->find($id)->status_id);
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\ContainerManagementController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ContainerOperationSearchTest extends TestCase
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
        $schema->create('statuses', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('display');
        });
        $schema->create('branches', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });
        $schema->create('container_datas', function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->string('do_code');
            $table->string('si_code');
            $table->unsignedBigInteger('si_destination_id');
            $table->string('si_destination_name');
            $table->unsignedInteger('status_id');
        });

        DB::table('statuses')->insert([
            ['id' => 12, 'display' => 'RETURNED TO DEPO'],
            ['id' => 18, 'display' => 'RECEIVED AT WH'],
        ]);
        DB::table('branches')->insert([
            ['id' => 2, 'name' => 'WH A'],
            ['id' => 3, 'name' => 'WH B'],
        ]);
        DB::table('container_datas')->insert([
            [
                'id' => 1,
                'code' => 'CONT-100',
                'do_code' => 'DO-100',
                'si_code' => 'SI-100',
                'si_destination_id' => 2,
                'si_destination_name' => 'WH A',
                'status_id' => 18,
            ],
            [
                'id' => 2,
                'code' => 'CONT-200',
                'do_code' => 'DO-200',
                'si_code' => 'SI-200',
                'si_destination_id' => 3,
                'si_destination_name' => 'WH B',
                'status_id' => 18,
            ],
            [
                'id' => 3,
                'code' => 'CONT-300',
                'do_code' => 'DO-300',
                'si_code' => 'SI-300',
                'si_destination_id' => 2,
                'si_destination_name' => 'WH A',
                'status_id' => 12,
            ],
        ]);

        $user = new User;
        $user->forceFill([
            'id' => 1,
        ]);
        $user->exists = true;
        $this->createSuperAdminRole();
        $this->actingAs($user);
    }

    public function test_received_operation_search_matches_container_do_and_si_codes(): void
    {
        $this->assertSame(['CONT-100'], $this->containerCodes(['search' => 'CONT-100']));
        $this->assertSame(['CONT-200'], $this->containerCodes(['search' => 'DO-200']));
        $this->assertSame(['CONT-100'], $this->containerCodes(['search' => 'SI-100']));
    }

    public function test_received_operation_search_keeps_status_and_destination_filters(): void
    {
        $this->assertSame([], $this->containerCodes(['search' => 'DO-300']));
        $this->assertSame(['CONT-100'], $this->containerCodes([
            'destination_id' => 2,
            'search' => 'CONT',
        ]));
        $this->assertSame([], $this->containerCodes(['search' => 'NOT-FOUND']));
    }

    public function test_all_and_returned_operation_search_matches_codes(): void
    {
        $this->assertSame(['CONT-300'], $this->containerCodes([
            'status' => 'all',
            'search' => 'SI-300',
        ]));
        $this->assertSame(['CONT-300'], $this->containerCodes([
            'status' => 'returned',
            'search' => 'DO-300',
        ]));
    }

    private function containerCodes(array $query): array
    {
        $request = Request::create('/container/operation', 'GET', array_merge([
            'status' => 'received',
        ], $query));

        $view = app(ContainerManagementController::class)->operation($request);
        $containers = $view->getData()['vueData']['containerList'];
        $codes = array_column($containers, 'code');
        sort($codes);

        return $codes;
    }
}

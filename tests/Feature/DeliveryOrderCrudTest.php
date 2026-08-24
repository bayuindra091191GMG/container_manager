<?php

namespace Tests\Feature;

use App\Http\Controllers\DeliveryOrderController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeliveryOrderCrudTest extends TestCase
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

        $schema->create('items', function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->string('name');
        });

        $schema->create('unit_materials', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });

        $schema->create('item_units', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('unit_id');
            $table->float('qty');
        });

        $schema->create('do_headers', function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->string('type');
            $table->dateTime('date');
            $table->string('so_code')->nullable();
            $table->string('dr_code')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('branch_destination_id')->nullable();
            $table->string('shipto_code')->nullable();
            $table->string('shipto_name')->nullable();
            $table->string('memo')->nullable();
            $table->string('shipping_company')->nullable();
            $table->unsignedBigInteger('container1_id')->nullable();
            $table->string('container1_code')->nullable();
            $table->unsignedBigInteger('container2_id')->nullable();
            $table->string('container2_code')->nullable();
            $table->string('transportation')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->boolean('is_close')->default(false);
            $table->unsignedBigInteger('closed_by_si_id')->nullable();
            $table->dateTime('voided_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        $schema->create('do_details', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('header_id');
            $table->string('transfer_no')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_name')->nullable();
            $table->float('qty')->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();
        });

        $schema->create('container_datas', function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->unsignedBigInteger('do_id')->nullable();
            $table->string('do_code')->nullable();
            $table->string('do_type')->nullable();
            $table->unsignedBigInteger('timbangan_id')->nullable();
            $table->string('timbangan_code')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->unsignedInteger('warehouse_location_id')->nullable();
            $table->string('warehouse_location_status')->nullable();
            $table->string('warehouse_area')->nullable();
            $table->string('warehouse_area_row')->nullable();
            $table->unsignedInteger('warehouse_status_id')->nullable();
            $table->string('warehouse_status')->nullable();
            $table->string('warehouse_status_reason')->nullable();
            $table->unsignedBigInteger('si_destination_id')->nullable();
            $table->string('si_destination_name')->nullable();
            $table->string('si_code')->nullable();
            $table->dateTime('si_etd_date')->nullable();
            $table->dateTime('si_eta_date')->nullable();
            $table->dateTime('free_storage_start_at')->nullable();
            $table->dateTime('free_storage_end_at')->nullable();
            $table->dateTime('free_demurrage_start_at')->nullable();
            $table->dateTime('free_demurrage_end_at')->nullable();
            $table->dateTime('last_status_updated_at')->nullable();
            $table->dateTime('berthing_at')->nullable();
            $table->dateTime('dooring_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->boolean('is_over_storage')->default(false);
            $table->dateTime('returned_at')->nullable();
            $table->boolean('is_over_demurrage')->default(false);
            $table->unsignedInteger('free_storage_duration')->default(0);
            $table->unsignedInteger('free_demurrage_duration')->default(0);
            $table->boolean('is_storage_monitor_start')->default(false);
            $table->boolean('is_storage_monitor_end')->default(false);
            $table->boolean('is_demurrage_monitor_start')->default(false);
            $table->boolean('is_demurrage_monitor_end')->default(false);
            $table->boolean('is_sync')->default(false);
            $table->string('item_name')->nullable();
            $table->string('vessel_name')->nullable();
            $table->timestamps();
        });

        DB::table('branches')->insert(['id' => 1, 'name' => 'WH MEDAN']);
        DB::table('branches')->insert(['id' => 2, 'name' => 'WH A']);
        DB::table('items')->insert([
            ['id' => 1, 'code' => 'ITM-1', 'name' => 'Cement Bag'],
            ['id' => 2, 'code' => 'ITM-2', 'name' => 'Ceramic Box'],
        ]);
        DB::table('unit_materials')->insert(['id' => 10, 'name' => 'TON']);
        DB::table('item_units')->insert([
            ['id' => 1, 'item_id' => 1, 'unit_id' => 10, 'qty' => 1],
        ]);
        DB::table('container_datas')->insert([
            'id' => 1,
            'code' => 'CONT-OLD',
            'status_id' => 18,
            'do_code' => 'OLD-DO',
            'si_code' => 'SI-OLD',
            'warehouse_status_id' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = new User;
        $user->forceFill([
            'id' => 1,
        ]);
        $user->exists = true;
        $this->createSuperAdminRole();
        $this->actingAs($user);
    }

    private function storeDo(array $overrides = []): JsonResponse
    {
        $payload = array_merge([
            'code' => 'DO-MANUAL-1',
            'type' => 'delivery_order',
            'date' => '21-08-2026',
            'so_code' => 'SO-1',
            'dr_code' => 'DR-1',
            'customer_code' => 'CUST-1',
            'customer_name' => 'Customer One',
            'shipto_code' => 'SHIP-1',
            'shipto_name' => 'Ship To One',
            'shipping_company' => 'Ship Co',
            'transportation' => 'TRUCK',
            'branch_destination' => 2,
            'container1_code' => 'CONT-OLD',
            'container2_code' => '',
            'details' => [
                ['item_id' => 1, 'qty' => '10', 'unit' => 'TON'],
                ['item_id' => 2, 'qty' => '5', 'unit' => 'BOX'],
            ],
        ], $overrides);

        return app(DeliveryOrderController::class)->store(Request::create('/delivery_order/store', 'POST', $payload));
    }

    public function test_store_creates_header_with_multiple_item_lines(): void
    {
        $response = $this->storeDo();

        $this->assertSame(200, $response->getStatusCode());
        $header = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->first();
        $this->assertNotNull($header);
        $this->assertSame(2, $header->branch_destination_id);
        $this->assertSame(1, $header->status_id);
        $this->assertSame(0, $header->is_close);

        $details = DB::table('do_details')->where('header_id', $header->id)->get();
        $this->assertCount(2, $details);
        $this->assertSame('ITM-1', $details[0]->item_code);
        $this->assertSame('ITM-2', $details[1]->item_code);
    }

    public function test_store_resets_existing_container_and_links_it(): void
    {
        $this->storeDo();

        $container = DB::table('container_datas')->where('code', 'CONT-OLD')->first();
        $this->assertSame(7, $container->status_id);
        $this->assertSame('DO-MANUAL-1', $container->do_code);
        $this->assertSame('DOORING', $container->warehouse_location_status);
        $this->assertSame(3, $container->warehouse_status_id);
        $this->assertNull($container->si_code);

        $header = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->first();
        $this->assertSame($container->id, $header->container1_id);
        $this->assertSame('CONT-OLD', $header->container1_code);
    }

    public function test_store_creates_missing_container_row(): void
    {
        $this->storeDo(['container1_code' => 'CONT-NEW']);

        $container = DB::table('container_datas')->where('code', 'CONT-NEW')->first();
        $this->assertNotNull($container);
        $this->assertSame(7, $container->status_id);
        $this->assertSame('DOORING', $container->warehouse_location_status);
        $this->assertSame('DO-MANUAL-1', $container->do_code);
    }

    public function test_store_rejects_duplicate_code(): void
    {
        $this->storeDo();
        $response = $this->storeDo();

        $payload = json_decode($response->getContent(), true);
        $this->assertSame('validation_failed', $payload['error']);
        $this->assertCount(1, DB::table('do_headers')->where('code', 'DO-MANUAL-1')->get());
    }

    public function test_store_rejects_identical_container_codes(): void
    {
        $response = $this->storeDo(['container1_code' => 'CONT-X', 'container2_code' => 'CONT-X']);

        $payload = json_decode($response->getContent(), true);
        $this->assertSame('validation_failed', $payload['error']);
        $this->assertCount(0, DB::table('do_headers')->where('code', 'DO-MANUAL-1')->get());
    }

    public function test_store_requires_at_least_one_detail_row(): void
    {
        $response = $this->storeDo(['details' => []]);

        $payload = json_decode($response->getContent(), true);
        $this->assertSame('validation_failed', $payload['error']);
    }

    public function test_item_select2_and_item_get_return_expected_shapes(): void
    {
        $request = Request::create('/delivery_order/item/select2', 'GET', ['search' => 'ITM']);
        $results = json_decode(app(DeliveryOrderController::class)->select2Item($request)->getContent(), true);
        $this->assertSame(1, $results[0]['id']);
        $this->assertSame('ITM-1 (Cement Bag)', $results[0]['text']);

        $get = Request::create('/delivery_order/item/get', 'POST', ['item_id' => 1]);
        $item = json_decode(app(DeliveryOrderController::class)->getItemData($get)->getContent(), true);
        $this->assertSame('ITM-1', $item['item_code']);
        $this->assertSame('TON', $item['unit']);
    }

    public function test_container_select2_searches_by_code(): void
    {
        $request = Request::create('/delivery_order/container/select2', 'GET', ['search' => 'CONT-OLD']);
        $results = json_decode(app(DeliveryOrderController::class)->select2Container($request)->getContent(), true);
        $this->assertSame('CONT-OLD', $results[0]['id']);
        $this->assertSame('CONT-OLD', $results[0]['text']);
    }

    private function updateDo(array $overrides = []): RedirectResponse
    {
        $headerId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');
        $payload = array_merge([
            'id' => $headerId,
            'code' => 'DO-MANUAL-1',
            'type' => 'delivery_order',
            'date' => '21-08-2026',
            'so_code' => 'SO-1',
            'dr_code' => 'DR-1',
            'customer_code' => 'CUST-1',
            'customer_name' => 'Customer One',
            'shipto_code' => 'SHIP-1',
            'shipto_name' => 'Ship To One',
            'shipping_company' => 'Ship Co',
            'transportation' => 'TRUCK',
            'branch_destination' => 2,
            'container1_code' => 'CONT-OLD',
            'container2_code' => '',
            'details' => [
                ['item_id' => 1, 'qty' => '20', 'unit' => 'TON'],
            ],
        ], $overrides);

        return app(DeliveryOrderController::class)->update(Request::create('/delivery_order/update', 'POST', $payload));
    }

    public function test_update_replaces_detail_lines(): void
    {
        $this->storeDo();
        $this->updateDo();

        $header = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->first();
        $details = DB::table('do_details')->where('header_id', $header->id)->get();
        $this->assertCount(1, $details);
        $this->assertSame('ITM-1', $details[0]->item_code);
        $this->assertSame(20.0, $details[0]->qty);

        $container = DB::table('container_datas')->where('code', 'CONT-OLD')->first();
        $this->assertSame(7, $container->status_id, 'update must not touch container rows');

        $this->assertSame('CONT-OLD', $header->container1_code);
        $this->assertSame($container->id, $header->container1_id);
        $this->assertNull($header->container2_code);
    }

    public function test_void_marks_open_do_as_voided(): void
    {
        $this->storeDo();
        $headerId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');

        app(DeliveryOrderController::class)->void(Request::create('/delivery_order/void', 'POST', ['id' => $headerId]));

        $header = DB::table('do_headers')->find($headerId);
        $this->assertSame(21, $header->status_id);
        $this->assertNotNull($header->voided_at);
    }

    public function test_void_is_blocked_on_closed_do(): void
    {
        $this->storeDo();
        DB::table('do_headers')->where('code', 'DO-MANUAL-1')->update(['is_close' => 1]);
        $headerId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');

        app(DeliveryOrderController::class)->void(Request::create('/delivery_order/void', 'POST', ['id' => $headerId]));

        $header = DB::table('do_headers')->find($headerId);
        $this->assertSame(1, $header->status_id);
        $this->assertNull($header->voided_at);
    }

    public function test_update_is_blocked_on_closed_do(): void
    {
        $this->storeDo();
        DB::table('do_headers')->where('code', 'DO-MANUAL-1')->update(['is_close' => 1]);
        $this->updateDo(['code' => 'DO-CHANGED']);

        $this->assertSame(0, DB::table('do_headers')->where('code', 'DO-CHANGED')->count());
        $this->assertSame(1, DB::table('do_headers')->where('code', 'DO-MANUAL-1')->count());
    }

    private function datatableDo(array $query = []): array
    {
        $request = Request::create('/delivery_order/datatable', 'GET', $query);
        $response = app(DeliveryOrderController::class)->datatable($request);

        return json_decode($response->getContent(), true)['data'];
    }

    public function test_datatable_shows_open_and_closed_but_not_voided(): void
    {
        $this->storeDo();
        $headerId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');
        DB::table('do_headers')->where('id', $headerId)->update(['is_close' => 1]);

        $this->storeDo(['code' => 'DO-VOIDED', 'container1_code' => '', 'container2_code' => '']);
        $voidedId = DB::table('do_headers')->where('code', 'DO-VOIDED')->value('id');
        DB::table('do_headers')->where('id', $voidedId)->update(['status_id' => 21]);

        $rows = $this->datatableDo();
        $codes = array_column($rows, 'code');
        sort($codes);

        $this->assertSame(['DO-MANUAL-1'], $codes);
    }

    public function test_datatable_embeds_detail_lines_per_header(): void
    {
        $this->storeDo();

        $rows = $this->datatableDo();
        $this->assertCount(1, $rows);
        $this->assertCount(2, $rows[0]['details']);
        $this->assertSame('ITM-1', $rows[0]['details'][0]['item_code']);
        $this->assertSame(10.0, (float) $rows[0]['details'][0]['qty']);
    }

    public function test_datatable_filters_by_is_close(): void
    {
        $this->storeDo();
        $headerId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');
        DB::table('do_headers')->where('id', $headerId)->update(['is_close' => 1]);
        $this->storeDo(['code' => 'DO-OPEN', 'container1_code' => '', 'container2_code' => '']);

        $openRows = $this->datatableDo(['status' => 0]);
        $this->assertSame(['DO-OPEN'], array_column($openRows, 'code'));

        $closedRows = $this->datatableDo(['status' => 1]);
        $this->assertSame(['DO-MANUAL-1'], array_column($closedRows, 'code'));
    }

    public function test_do_select2_excludes_multi_line_dos(): void
    {
        $this->storeDo();
        $this->storeDo(['code' => 'DO-SINGLE', 'container1_code' => '', 'container2_code' => '', 'details' => [
            ['item_id' => 1, 'qty' => '3', 'unit' => 'TON'],
        ]]);

        $request = Request::create('/delivery_order/select2', 'GET', ['search' => 'DO-']);
        $results = json_decode(app(DeliveryOrderController::class)->select2($request)->getContent(), true);
        $ids = array_column($results, 'id');

        $multiLineId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');
        $singleLineId = DB::table('do_headers')->where('code', 'DO-SINGLE')->value('id');

        $this->assertNotContains($multiLineId, $ids);
        $this->assertContains($singleLineId, $ids);
    }

    public function test_get_container_data_rejects_multi_line_do(): void
    {
        $this->storeDo();
        $multiLineId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');

        $request = Request::create('/delivery_order/container/get', 'POST', ['do_id' => $multiLineId]);
        $response = app(DeliveryOrderController::class)->getContainerData($request);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertArrayHasKey('error', json_decode($response->getContent(), true));
    }

    public function test_get_container_data_rejects_voided_do(): void
    {
        $this->storeDo();
        $voidedId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');
        DB::table('do_headers')->where('id', $voidedId)->update(['status_id' => 21]);

        $request = Request::create('/delivery_order/container/get', 'POST', ['do_id' => $voidedId]);
        $response = app(DeliveryOrderController::class)->getContainerData($request);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function test_show_redirects_for_voided_do(): void
    {
        $this->storeDo();
        $voidedId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');
        DB::table('do_headers')->where('id', $voidedId)->update(['status_id' => 21]);

        $response = app(DeliveryOrderController::class)->show($voidedId);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('delivery_order'), $response->getTargetUrl());
    }

    public function test_select2_item_resolves_by_id(): void
    {
        $request = Request::create('/delivery_order/item/select2', 'GET', ['id' => 1]);
        $results = json_decode(app(DeliveryOrderController::class)->select2Item($request)->getContent(), true);

        $this->assertCount(1, $results);
        $this->assertSame(1, $results[0]['id']);
        $this->assertSame('ITM-1 (Cement Bag)', $results[0]['text']);
    }

    public function test_edit_detail_list_contains_item_select_extra(): void
    {
        $this->storeDo(['container1_code' => '', 'container2_code' => '', 'details' => [
            ['item_id' => 1, 'qty' => '3', 'unit' => 'TON'],
        ]]);
        $headerId = DB::table('do_headers')->where('code', 'DO-MANUAL-1')->value('id');

        $request = Request::create('/delivery_order/edit/'.$headerId, 'GET');
        $response = app(DeliveryOrderController::class)->edit($headerId);

        $detailList = $response->getData()['detailList'];
        $this->assertSame(1, $detailList[0]['item_select_extra']['id']);
    }
}

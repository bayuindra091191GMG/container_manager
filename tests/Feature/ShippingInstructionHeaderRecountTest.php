<?php

namespace Tests\Feature;

use App\Models\ShippingInstructionHeader;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ShippingInstructionHeaderRecountTest extends TestCase
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
        $schema->create('shipping_instruction_headers', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->nullable();
            $table->unsignedInteger('total_berthing_cont')->default(0);
            $table->unsignedInteger('total_dooring_cont')->default(0);
            $table->unsignedInteger('total_received_cont')->default(0);
        });
        $schema->create('shipping_instruction_monitors', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('header_id');
            $table->string('header_code')->nullable();
            $table->string('container_code')->nullable();
            $table->unsignedInteger('status_id');
        });

        DB::table('shipping_instruction_headers')->insert([
            ['id' => 1, 'code' => 'SI-1', 'total_berthing_cont' => 99, 'total_dooring_cont' => 99, 'total_received_cont' => 99],
            ['id' => 2, 'code' => 'SI-2', 'total_berthing_cont' => 0, 'total_dooring_cont' => 0, 'total_received_cont' => 0],
        ]);
        DB::table('shipping_instruction_monitors')->insert([
            ['header_id' => 1, 'header_code' => 'SI-1', 'container_code' => 'C-1', 'status_id' => 9],
            ['header_id' => 1, 'header_code' => 'SI-1', 'container_code' => 'C-2', 'status_id' => 13],
            ['header_id' => 1, 'header_code' => 'SI-1', 'container_code' => 'C-3', 'status_id' => 13],
            ['header_id' => 1, 'header_code' => 'SI-1', 'container_code' => 'C-4', 'status_id' => 18],
            ['header_id' => 1, 'header_code' => 'SI-1', 'container_code' => 'C-5', 'status_id' => 7],
        ]);
    }

    public function test_recount_container_totals_counts_berthing_dooring_and_received(): void
    {
        ShippingInstructionHeader::recountContainerTotals(1);

        $header = DB::table('shipping_instruction_headers')->where('id', 1)->first();
        $this->assertSame(1, $header->total_berthing_cont);
        $this->assertSame(2, $header->total_dooring_cont);
        $this->assertSame(1, $header->total_received_cont);
    }

    public function test_recount_container_totals_sets_zero_when_no_monitors(): void
    {
        ShippingInstructionHeader::recountContainerTotals(2);

        $header = DB::table('shipping_instruction_headers')->where('id', 2)->first();
        $this->assertSame(0, $header->total_berthing_cont);
        $this->assertSame(0, $header->total_dooring_cont);
        $this->assertSame(0, $header->total_received_cont);
    }
}

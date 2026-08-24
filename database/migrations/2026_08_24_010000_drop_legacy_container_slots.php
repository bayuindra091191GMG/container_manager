<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $invalidDos = DB::table('do_headers')
            ->where(function ($query): void {
                $query->whereNull('container1_code')
                    ->orWhere('container1_code', '');
            })
            ->count();
        $legacyDos = DB::table('do_headers')
            ->where(function ($query): void {
                $query->whereNotNull('container2_id')
                    ->orWhere(function ($query): void {
                        $query->whereNotNull('container2_code')
                            ->where('container2_code', '<>', '');
                    });
            })
            ->count();
        $legacySiDetails = DB::table('shipping_instruction_details')
            ->where(function ($query): void {
                $query->whereNotNull('container2_id')
                    ->orWhere(function ($query): void {
                        $query->whereNotNull('container2_code')
                            ->where('container2_code', '<>', '');
                    });
            })
            ->count();

        if ($invalidDos > 0 || $legacyDos > 0 || $legacySiDetails > 0) {
            throw new RuntimeException(
                'Single-container migration blocked: resolve invalid container1 rows and legacy container2 data first.'
            );
        }

        Schema::table('do_headers', function (Blueprint $table): void {
            $table->dropColumn(['container2_id', 'container2_code']);
        });

        Schema::table('shipping_instruction_details', function (Blueprint $table): void {
            $table->dropColumn(['container2_id', 'container2_code']);
        });
    }

    public function down(): void
    {
        Schema::table('do_headers', function (Blueprint $table): void {
            $table->bigInteger('container2_id')->nullable()->after('container1_code');
            $table->string('container2_code', 200)
                ->charset('utf8mb4')
                ->collation('utf8mb4_unicode_ci')
                ->nullable()
                ->after('container2_id');
        });

        Schema::table('shipping_instruction_details', function (Blueprint $table): void {
            $table->bigInteger('container2_id')->nullable()->after('container1_code');
            $table->string('container2_code', 200)
                ->charset('utf8mb4')
                ->collation('utf8mb4_unicode_ci')
                ->nullable()
                ->after('container2_id');
        });
    }
};

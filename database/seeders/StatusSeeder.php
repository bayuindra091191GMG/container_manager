<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            1 => 'ACTIVE',
            2 => 'INACTIVE',
            3 => 'FULL',
            4 => 'EMPTY_GOOD',
            5 => 'BROKEN',
            7 => 'PENDING',
            8 => 'SAILING',
            9 => 'BERTHING',
            12 => 'RETURNED TO DEPO',
            13 => 'DOORING',
            14 => 'AT FULL STORAGE',
            15 => 'STAGING TO UNLOAD',
            16 => 'AT EMPTY STORAGE',
            17 => 'AT BROKEN STORAGE',
            18 => 'RECEIVED AT WH',
            21 => 'VOID',
        ];

        foreach ($statuses as $id => $display) {
            Status::query()->firstOrCreate(
                ['id' => $id],
                ['description' => strtolower($display), 'display' => $display],
            );
        }
    }
}

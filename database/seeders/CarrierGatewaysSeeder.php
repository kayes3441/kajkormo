<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CarrierGatewaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run():void
    {
        $carriers = [
            'rtd',
            'dhl_express',
            'dhl_ecommerce',
            'fedex',
            'ups',
            'usps',
            'aramex',
        ];

        $now = now();

        $records = array_map(function ($key) use ($now) {
            return [
                'id' => Str::uuid(),
                'key' => $key,
                'gateway_values' => json_encode([
                    'api_key' => strtoupper($key) . '_API_KEY',
                    'api_secret' => 'secret_' . $key,
                    'account_number' => rand(1000000000, 9999999999),
                ]),
                'additional_data' => json_encode([
                    'region' => 'global',
                    'supports_cod' => (bool)rand(0, 1),
                ]),

                'status' => (bool)rand(0, 1),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $carriers);

        DB::table('carrier_gateways')->insert($records);
    }
}

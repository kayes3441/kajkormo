<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        foreach ($this->getCountryListArray() as $country) {
            DB::table('countries')->updateOrInsert(
                ['iso2' => $country['iso2']],
                array_merge($country, [
                    'location_labels' => json_encode($country['location_labels']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
    private function getCountryListArray():array
    {
        return  [
            [
                'name' => 'Bangladesh',
                'iso2' => 'BD',
                'iso3' => 'BGD',
                'numeric_code' => '050',
                'phone_code' => '880',
                'currency' => 'BDT',
                'currency_name' => 'Bangladeshi Taka',
                'currency_symbol' => '৳',
                'location_labels' => ['division','metropolitan' ,'district', 'sub-district'],
                'timezones' => 'Asia/Dhaka',
                'latitude' => 23.6850,
                'longitude' => 90.3563,
                'is_active' => true,
            ],
        ];
    }
}

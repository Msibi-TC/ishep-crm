<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'EC' => 'Eastern Cape', 'FS' => 'Free State', 'GP' => 'Gauteng',
            'KZN' => 'KwaZulu-Natal', 'LP' => 'Limpopo', 'MP' => 'Mpumalanga',
            'NC' => 'Northern Cape', 'NW' => 'North West', 'WC' => 'Western Cape',
        ] as $code => $name) {
            Province::updateOrCreate(['code' => $code], ['name' => $name, 'active' => true]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxBand;

class TaxBandSeeder extends Seeder
{
    public function run()
    {
        TaxBand::insert([
            [
                'lower_limit' => 0,
                'upper_limit' => 5000,
                'tax_rate' => 0,
            ],
            [
                'lower_limit' => 5000,
                'upper_limit' => 20000,
                'tax_rate' => 20,
            ],
            [
                'lower_limit' => 20000,
                'upper_limit' => null,
                'tax_rate' => 40,
            ],
        ]);
    }
}


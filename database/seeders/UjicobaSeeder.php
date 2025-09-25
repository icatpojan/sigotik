<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ujicoba;

class UjicobaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ujicoba = [
            ['no' => 1],
            ['no' => 2],
            ['no' => 3],
        ];

        foreach ($ujicoba as $item) {
            Ujicoba::create($item);
        }
    }
}

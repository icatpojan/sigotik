<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfGroup;

class ConfGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            ['conf_group_id' => 1, 'group' => 'Admins'],
            ['conf_group_id' => 2, 'group' => 'UPT'],
            ['conf_group_id' => 3, 'group' => 'KAPAL'],
            ['conf_group_id' => 5, 'group' => 'PUSAT'],
        ];

        foreach ($groups as $group) {
            ConfGroup::updateOrCreate(
                ['conf_group_id' => $group['conf_group_id']],
                $group
            );
        }
    }
}

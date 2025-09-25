<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            ['id' => 12, 'name' => 'Beranda', 'url' => 'home', 'parent_id' => 0, 'status' => 1, 'position' => 12, 'type' => null],
            ['id' => 14, 'name' => 'Berita', 'url' => 'posts', 'parent_id' => 0, 'status' => 1, 'position' => 14, 'type' => null],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['id' => $menu['id']],
                $menu
            );
        }
    }
}

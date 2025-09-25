<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['id' => 1, 'name' => 'Pengumuman', 'slug' => 'pengumuman', 'status' => 1],
            ['id' => 2, 'name' => 'Agenda', 'slug' => 'agenda', 'status' => 1],
            ['id' => 3, 'name' => 'Artikel', 'slug' => 'artikel', 'status' => 1],
            ['id' => 4, 'name' => 'Pelayanan', 'slug' => 'pelayanan', 'status' => 1],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],
                $category
            );
        }
    }
}

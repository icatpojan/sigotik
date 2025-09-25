<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\ConfUser;

class ConfUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah user admin sudah ada
        $adminExists = ConfUser::where('username', 'admin')->exists();

        if (!$adminExists) {
            ConfUser::create([
                'conf_user_id' => 1,
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'm_upt_code' => '000',
                'conf_group_id' => 1,
                'email' => 'admin@sigotik.com',
                'is_active' => '1',
                'nama_lengkap' => 'Administrator',
                'nip' => '000000000000000000',
                'golongan' => 'IV/a',
                'date_insert' => now(),
                'user_insert' => 'system',
                'date_update' => now(),
                'user_update' => 'system'
            ]);
        }
    }
}

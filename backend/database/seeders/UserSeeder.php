<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'Andi Multimedia',
            'email' => 'Andimultimedia@papua.go.id',
            'password' => bcrypt('papua1324'),
        ]);

        // Create associated pegawai record
        Pegawai::create([
            'user_id' => $user->id,
            'nip' => '19900101001',
            'nama' => 'Andi Multimedia',
            'jabatan' => 'Staff IT',
            'unit_kerja' => 'Dinas Kepegawaian',
        ]);
    }
}

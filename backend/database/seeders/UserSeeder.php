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
            'name' => 'Andi Kasongat',
            'email' => 'Andimultimedia@papua.go.id',
            'password' => bcrypt('papua1324'),
        ]);

        // Create associated pegawai record
        Pegawai::create([
            'user_id' => $user->id,
            'nip' => '199407132022111001',
            'nama' => 'Andi Kasongat',
            'jabatan' => 'Staff',
            'unit_kerja' => 'Dinas Komunikasi dan Informatika',
        ]);
    }
}

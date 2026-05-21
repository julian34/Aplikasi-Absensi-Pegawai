<?php

namespace Tests\Feature\Auth;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginPegawaiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_dapat_login_menggunakan_email_dan_password_valid(): void
    {
        $user = User::create([
            'name' => 'Pegawai Test',
            'email' => 'pegawai@test.com',
            'password' => Hash::make('password123'),
        ]);

        Pegawai::create([
            'user_id' => $user->id,
            'nip' => '12345678901',
            'nama' => 'Pegawai Test',
            'jabatan' => 'Staff',
            'unit_kerja' => 'Kepegawaian',
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'pegawai@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user',
            ])
            ->assertJson([
                'message' => 'Login berhasil',
            ]);
    }

    public function test_pegawai_dapat_login_menggunakan_nip_dan_password_valid(): void
    {
        $user = User::create([
            'name' => 'Pegawai Test',
            'email' => 'pegawai@test.com',
            'password' => Hash::make('password123'),
        ]);

        Pegawai::create([
            'user_id' => $user->id,
            'nip' => '12345678901',
            'nama' => 'Pegawai Test',
            'jabatan' => 'Staff',
            'unit_kerja' => 'Kepegawaian',
        ]);

        $response = $this->postJson('/api/login', [
            'login' => '12345678901',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user',
            ])
            ->assertJson([
                'message' => 'Login berhasil',
            ]);
    }

    public function test_login_gagal_jika_password_salah(): void
    {
        User::create([
            'name' => 'Pegawai Test',
            'email' => 'pegawai@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'pegawai@test.com',
            'password' => 'password_salah',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_gagal_jika_input_kosong(): void
    {
        $response = $this->postJson('/api/login', [
            'login' => '',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'login',
                'password',
            ]);
    }
}
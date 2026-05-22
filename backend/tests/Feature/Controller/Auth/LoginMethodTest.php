<?php

namespace Tests\Feature\Controller\Auth;
use Tests\Feature\Controller\ControllerTestCase;

class LoginMethodTest extends ControllerTestCase
{   

    // Test untuk memastikan login dengan email berhasil
    public function test_login_email_valid_berhasil(): void
    {
        // Buat user dengan email dan password yang sesuai untuk test ini
        $this->makeUserWithPegawai();

        // Lakukan request login dengan email dan password yang benar
        $response = $this->postJson('/api/login', [
            'login' => 'julian_allen@papua.go.id',
            'password' => 'papua',
        ]);

        // Pastikan response memiliki status OK dan struktur JSON yang benar
        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'user',
            ]);
    }

    // Test untuk memastikan login dengan NIP berhasil
    public function test_login_nip_valid_berhasil(): void
    {
        // Buat user dengan NIP dan password yang sesuai untuk test ini
        $this->makeUserWithPegawai();

        // Lakukan request login dengan NIP dan password yang benar
        $response = $this->postJson('/api/login', [
            'login' => '199507132020111001',
            'password' => 'papua',
        ]);

        // Pastikan response memiliki status OK dan struktur JSON yang benar
        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'user',
            ]);
    }

    // Test untuk memastikan login dengan input kosong ditolak
    public function test_login_input_kosong_ditolak(): void
    {

        // Lakukan request login dengan input kosong
        $response = $this->postJson('/api/login', [
            'login' => '',
            'password' => '',
        ]);

        // Pastikan response memiliki status Unprocessable Entity dan terdapat error validasi untuk login dan password
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'login',
                'password',
            ]);
    }

    // Test untuk memastikan login dengan NIP yang tidak ditemukan ditolak
    public function test_login_nip_tidak_ditemukan_ditolak(): void
    {
        // Buat user dengan NIP yang berbeda untuk memastikan NIP yang digunakan dalam test ini tidak ditemukan
        $response = $this->postJson('/api/login', [
            'login' => '199507132020111002',
            'password' => 'password123',
        ]);

        // Pastikan response memiliki status Unprocessable Entity dan terdapat error validasi untuk login
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    // Test untuk memastikan login dengan password yang salah ditolak
    public function test_login_password_salah_ditolak(): void
    {
        // Buat user dengan email dan password yang sesuai untuk test ini
        $this->makeUserWithPegawai();

        // Lakukan request login dengan email yang benar tetapi password yang salah
        $response = $this->postJson('/api/login', [
            'login' => 'julian_allen@papua.go.id',
            'password' => '13213123',
        ]);

        // Pastikan response memiliki status Unprocessable Entity dan terdapat error validasi untuk password
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
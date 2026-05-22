<?php

namespace Tests\Feature\Controller\Absensi;

use Laravel\Sanctum\Sanctum;
use Tests\Feature\Controller\ControllerTestCase;

class TodayMethodTest extends ControllerTestCase
{
    public function test_today_mengembalikan_absensi_hari_ini(): void
    {
        // freeze waktu untuk memastikan data absensi yang dibuat memiliki tanggal yang sesuai dengan hari ini
        $this->freeze('2026-05-22 07:00:00');

        // login sebagai pegawai dan dapatkan data pegawai yang login
        [, $pegawai] = $this->actingAsPegawai();

        // buat data absensi untuk pegawai yang login dengan tanggal hari ini
        $this->makeAbsensi($pegawai, [
            'tanggal' => '2026-05-22',
        ]);

        // lakukan request ke endpoint /api/absensi/today
        $response = $this->getJson('/api/absensi/today');

        // assert bahwa response memiliki status 200 dan data absensi yang dikembalikan sesuai dengan data absensi yang dibuat
        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_today_ditolak_jika_data_pegawai_tidak_ada(): void
    {
        // buat user baru tanpa data pegawai
        $user = $this->makeUser();

        // login sebagai user tersebut
        Sanctum::actingAs($user);

        // lakukan request ke endpoint /api/absensi/today
        $response = $this->getJson('/api/absensi/today');

        // assert bahwa response memiliki status 404
        $response->assertStatus(404);
    }

    public function test_today_ditolak_jika_belum_login(): void
    {
        // lakukan request ke endpoint /api/absensi/today tanpa login
        $response = $this->getJson('/api/absensi/today');

        // assert bahwa response memiliki status 401
        $response->assertStatus(401);
    }
}
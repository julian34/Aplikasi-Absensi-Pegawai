<?php

namespace Tests\Feature\Controller\Absensi;

use Laravel\Sanctum\Sanctum;
use Tests\Feature\Controller\ControllerTestCase;

class AbsenDatangMethodTest extends ControllerTestCase
{
    // test untuk memastikan bahwa absen datang berhasil dengan status tepat waktu jika absen dilakukan sebelum pukul 08:00
    public function test_absen_datang_berhasil_tepat_waktu(): void
    {
        $this->freeze('2026-05-21 07:45:00');

        [, $pegawai] = $this->actingAsPegawai();

        $response = $this->postJson('/api/absensi/datang');

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status_masuk', 'tepat_waktu');

        $this->assertDatabaseHas('absensi', [
            'pegawai_id' => $pegawai->id,
            'tanggal' => '2026-05-21',
            'status_masuk' => 'tepat_waktu',
        ]);
    }

    // test untuk memastikan bahwa absen datang berhasil dengan status terlambat jika absen dilakukan setelah pukul 08:00
    public function test_absen_datang_berhasil_terlambat(): void
    {
        $this->freeze('2026-05-21 08:10:00');

        [, $pegawai] = $this->actingAsPegawai();

        $response = $this->postJson('/api/absensi/datang');

        $response->assertStatus(201)
            ->assertJsonPath('data.status_masuk', 'terlambat');

        $this->assertDatabaseHas('absensi', [
            'pegawai_id' => $pegawai->id,
            'tanggal' => '2026-05-21',
            'status_masuk' => 'terlambat',
        ]);
    }

    // test untuk memastikan bahwa absen datang ditolak jika sudah melakukan absen datang hari ini
    public function test_absen_datang_ditolak_jika_sudah_absen(): void
    {
        $this->freeze('2026-05-21 08:00:00');

        [, $pegawai] = $this->actingAsPegawai();

        $this->makeAbsensi($pegawai, [
            'tanggal' => '2026-05-21',
        ]);

        $response = $this->postJson('/api/absensi/datang');

        $response->assertStatus(422);
    }

    // test untuk memastikan bahwa absen datang ditolak jika hari ini adalah hari libur
    public function test_absen_datang_ditolak_pada_hari_libur(): void
    {
        $this->freeze('2026-05-23 08:00:00');

        $this->actingAsPegawai();

        $response = $this->postJson('/api/absensi/datang');

        $response->assertStatus(422);
    }

    // test untuk memastikan bahwa absen datang ditolak jika user tidak punya data pegawai
    public function test_absen_datang_ditolak_jika_user_tidak_punya_data_pegawai(): void
    {
        $this->freeze('2026-05-21 08:00:00');

        $user = $this->makeUser();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/absensi/datang');

        $response->assertStatus(404);
    }

    // test untuk memastikan bahwa absen datang ditolak jika belum login
    public function test_absen_datang_ditolak_jika_belum_login(): void
    {
        $response = $this->postJson('/api/absensi/datang');

        $response->assertStatus(401);
    }
}
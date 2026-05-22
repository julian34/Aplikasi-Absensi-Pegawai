<?php

namespace Tests\Feature\Controller\Absensi;

use Laravel\Sanctum\Sanctum;
use Tests\Feature\Controller\ControllerTestCase;

class AbsenPulangMethodTest extends ControllerTestCase
{
    // test untuk memastikan bahwa absen pulang berhasil dengan status sesuai jam jika absen dilakukan pada pukul 16:00 atau lebih
    public function test_absen_pulang_berhasil_sesuai_jam(): void
    {
        $this->freeze('2026-05-22 16:00:00');

        [, $pegawai] = $this->actingAsPegawai();

        $this->makeAbsensi($pegawai, [
            'tanggal' => '2026-05-22',
            'jam_pulang' => null,
        ]);

        $response = $this->postJson('/api/absensi/pulang');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status_pulang', 'sesuai_jam');
    }

    // test untuk memastikan bahwa absen pulang berhasil dengan status pulang cepat jika absen dilakukan sebelum pukul 16:00
    public function test_absen_pulang_berhasil_pulang_cepat(): void
    {
        $this->freeze('2026-05-22 15:20:00');

        [, $pegawai] = $this->actingAsPegawai();

        $this->makeAbsensi($pegawai, [
            'tanggal' => '2026-05-22',
            'jam_pulang' => null,
        ]);

        $response = $this->postJson('/api/absensi/pulang');

        $response->assertOk()
            ->assertJsonPath('data.status_pulang', 'pulang_cepat');
    }

    // test untuk memastikan bahwa absen pulang ditolak jika belum melakukan absen datang
    public function test_absen_pulang_ditolak_jika_belum_absen_datang(): void
    {
        $this->freeze('2026-05-22 16:00:00');

        $this->actingAsPegawai();

        $response = $this->postJson('/api/absensi/pulang');

        $response->assertStatus(422);
    }

    // test untuk memastikan bahwa absen pulang ditolak jika sudah melakukan absen pulang hari ini
    public function test_absen_pulang_ditolak_jika_sudah_absen_pulang(): void
    {
        $this->freeze('2026-05-22 17:00:00');

        [, $pegawai] = $this->actingAsPegawai();

        $this->makeAbsensi($pegawai, [
            'tanggal' => '2026-05-21',
            'jam_pulang' => '16:00:00',
            'status_pulang' => 'sesuai_jam',
            'status' => 'hadir',
        ]);

        $response = $this->postJson('/api/absensi/pulang');

        $response->assertStatus(422);
    }

    // test untuk memastikan bahwa absen pulang ditolak jika hari ini adalah hari libur
    public function test_absen_pulang_ditolak_pada_hari_libur(): void
    {
        // absen pulang ketika tanggal 23/mei/2026, Hari minggu
        $this->freeze('2026-05-23 16:00:00');

        $this->actingAsPegawai();

        $response = $this->postJson('/api/absensi/pulang');

        $response->assertStatus(422);
    }

    // test untuk memastikan bahwa absen pulang ditolak jika user tidak punya data pegawai
    public function test_absen_pulang_ditolak_jika_user_tidak_punya_data_pegawai(): void
    {
        $this->freeze('2026-05-22 16:00:00');

        $user = $this->makeUser();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/absensi/pulang');

        $response->assertStatus(404);
    }

    // test untuk memastikan bahwa absen pulang ditolak jika belum login
    public function test_absen_pulang_ditolak_jika_belum_login(): void
    {
        $response = $this->postJson('/api/absensi/pulang');

        $response->assertStatus(401);
    }
}
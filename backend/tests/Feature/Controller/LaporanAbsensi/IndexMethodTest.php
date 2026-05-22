<?php

namespace Tests\Feature\Controller\LaporanAbsensi;

use Laravel\Sanctum\Sanctum;
use Tests\Feature\Controller\ControllerTestCase;

class IndexMethodTest extends ControllerTestCase
{
    // test untuk memastikan bahwa index method berhasil mengembalikan laporan bulanan dengan data yang sesuai
    public function test_index_berhasil_mengambil_laporan_bulanan(): void
    {
        [, $pegawai] = $this->actingAsPegawai();

        // buat data absensi untuk pegawai yang login dengan tanggal di bulan yang sama dengan parameter bulan dan tahun yang dikirimkan
        $this->makeAbsensi($pegawai, [
            'tanggal' => '2026-05-01',
            'status' => 'hadir',
            'jam_pulang' => '16:00:00',
        ]);

        // lakukan request ke endpoint /api/laporan-absensi dengan parameter bulan dan tahun yang sesuai dengan data absensi yang dibuat
        $response = $this->getJson('/api/laporan-absensi?bulan=5&tahun=2026');

        // assert bahwa response memiliki status 200 dan data laporan yang dikembalikan sesuai dengan data absensi yang dibuat
        $response->assertOk()
            ->assertJsonPath('success', true);
    }


    // test untuk memastikan bahwa index method mengembalikan laporan bulanan dengan data absensi yang sesuai dengan parameter bulan dan tahun yang dikirimkan
    public function test_index_ditolak_jika_bulan_tidak_valid(): void
    {

        // login sebagai pegawai untuk mendapatkan data pegawai yang login
        $this->actingAsPegawai();

        // lakukan request ke endpoint /api/laporan-absensi dengan parameter bulan yang tidak valid (misalnya 13)
        $response = $this->getJson('/api/laporan-absensi?bulan=13&tahun=2026');

        // assert bahwa response memiliki status 422
        $response->assertStatus(422);
    }


    // test untuk memastikan bahwa index method mengembalikan laporan bulanan dengan data absensi yang sesuai dengan parameter bulan dan tahun yang dikirimkan
    public function test_index_ditolak_jika_user_tidak_punya_data_pegawai(): void
    {
        // buat user baru tanpa data pegawai
        $user = $this->makeUser();

        // login sebagai user tersebut
        Sanctum::actingAs($user);

        // lakukan request ke endpoint /api/laporan-absensi dengan parameter bulan dan tahun yang sesuai
        $response = $this->getJson('/api/laporan-absensi?bulan=5&tahun=2026');

        // assert bahwa response memiliki status 404
        $response->assertStatus(404);
    }


    // test untuk memastikan bahwa index method ditolak jika belum login
    public function test_index_ditolak_jika_belum_login(): void
    {

        // lakukan request ke endpoint /api/laporan-absensi dengan parameter bulan dan tahun yang sesuai tanpa login
        $response = $this->getJson('/api/laporan-absensi?bulan=5&tahun=2026');

        // assert bahwa response memiliki status 401
        $response->assertStatus(401);
    }
}
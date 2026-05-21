<?php

namespace Tests\Feature\Absensi;

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AbsensiPegawaiTest extends TestCase
{
    use RefreshDatabase;

    private function buatPegawaiLogin(): array
    {
        $user = User::create([
            'name' => 'Pegawai Test',
            'email' => 'pegawai@test.com',
            'password' => Hash::make('password123'),
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $user->id,
            'nip' => '12345678901',
            'nama' => 'Pegawai Test',
            'jabatan' => 'Staff',
            'unit_kerja' => 'Kepegawaian',
        ]);

        Sanctum::actingAs($user);

        return [$user, $pegawai];
    }

    public function test_pegawai_dapat_melihat_absensi_hari_ini(): void
    {
        [$user, $pegawai] = $this->buatPegawaiLogin();

        $response = $this->getJson('/api/absensi/today');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data absensi hari ini berhasil diambil.',
            ]);
    }

    public function test_pegawai_dapat_absen_datang(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 07:30:00', 'Asia/Jakarta'));

        [$user, $pegawai] = $this->buatPegawaiLogin();

        $response = $this->postJson('/api/absensi/datang');

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Absen datang berhasil.',
            ]);

        $this->assertDatabaseHas('absensi', [
            'pegawai_id' => $pegawai->id,
            'tanggal' => '2026-05-21',
            'status' => 'absen_sekali',
            'status_masuk' => 'tepat_waktu',
        ]);

        Carbon::setTestNow();
    }

    public function test_pegawai_tidak_bisa_absen_datang_dua_kali(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 07:30:00', 'Asia/Jakarta'));

        [$user, $pegawai] = $this->buatPegawaiLogin();

        Absensi::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => '2026-05-21',
            'jam_masuk' => '07:30:00',
            'jam_pulang' => null,
            'status_masuk' => 'tepat_waktu',
            'status_pulang' => null,
            'status' => 'absen_sekali',
            'keterangan' => 'Absen datang tepat waktu.',
        ]);

        $response = $this->postJson('/api/absensi/datang');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Anda sudah melakukan absen datang hari ini.',
            ]);

        Carbon::setTestNow();
    }

    public function test_pegawai_tidak_bisa_absen_pulang_sebelum_absen_datang(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 16:00:00', 'Asia/Jakarta'));

        [$user, $pegawai] = $this->buatPegawaiLogin();

        $response = $this->postJson('/api/absensi/pulang');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Anda belum melakukan absen datang hari ini.',
            ]);

        Carbon::setTestNow();
    }

    public function test_pegawai_dapat_absen_pulang_setelah_absen_datang(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 16:00:00', 'Asia/Jakarta'));

        [$user, $pegawai] = $this->buatPegawaiLogin();

        Absensi::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => '2026-05-21',
            'jam_masuk' => '07:30:00',
            'jam_pulang' => null,
            'status_masuk' => 'tepat_waktu',
            'status_pulang' => null,
            'status' => 'absen_sekali',
            'keterangan' => 'Absen datang tepat waktu.',
        ]);

        $response = $this->postJson('/api/absensi/pulang');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Absen pulang berhasil.',
            ]);

        $this->assertDatabaseHas('absensi', [
            'pegawai_id' => $pegawai->id,
            'tanggal' => '2026-05-21',
            'status' => 'hadir',
            'status_pulang' => 'sesuai_jam',
        ]);

        Carbon::setTestNow();
    }

    public function test_pegawai_tidak_bisa_absen_pulang_dua_kali(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 16:30:00', 'Asia/Jakarta'));

        [$user, $pegawai] = $this->buatPegawaiLogin();

        Absensi::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => '2026-05-21',
            'jam_masuk' => '07:30:00',
            'jam_pulang' => '16:00:00',
            'status_masuk' => 'tepat_waktu',
            'status_pulang' => 'sesuai_jam',
            'status' => 'hadir',
            'keterangan' => 'Absen datang tepat waktu. Absen pulang sesuai jam kerja.',
        ]);

        $response = $this->postJson('/api/absensi/pulang');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Anda sudah melakukan absen pulang hari ini.',
            ]);

        Carbon::setTestNow();
    }

    public function test_endpoint_absensi_ditolak_jika_belum_login(): void
    {
        $response = $this->postJson('/api/absensi/datang');

        $response->assertStatus(401);
    }
}
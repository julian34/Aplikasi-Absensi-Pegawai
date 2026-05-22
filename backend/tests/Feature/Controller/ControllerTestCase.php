<?php

namespace Tests\Feature\Controller;

// Import model 
use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\User;

// Import kelas dan trait yang diperlukan untuk testing
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class ControllerTestCase extends TestCase
{
    use RefreshDatabase;

    // Pastikan untuk mengembalikan waktu ke kondisi normal setelah setiap test
    protected function tearDown(): void
    {
        // Pastikan untuk mengembalikan waktu ke kondisi normal setelah setiap test
        Carbon::setTestNow();

        // Panggil parent tearDown untuk memastikan database di-refresh dengan benar
        parent::tearDown();
    }

    // Metode untuk membekukan waktu selama test, dengan format datetime yang dapat disesuaikan
    protected function freeze(string $datetime): void
    {
        // Pastikan untuk menggunakan zona waktu yang sesuai dengan aplikasi Anda
        Carbon::setTestNow(Carbon::parse($datetime, 'Asia/Jakarta'));
    }

    // Metode untuk membuat user baru dengan email dan password yang dapat disesuaikan
    protected function makeUser(
        string $email = 'julian_allen@papua.go.id',
        string $password = 'papua'
    ): User {

    // Pastikan email unik untuk setiap test case
        return User::create([
            'name' => 'julian allen',
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    // Metode untuk membuat user baru dengan pegawai terkait, dengan email, NIP, dan password yang dapat disesuaikan
    protected function makeUserWithPegawai(
        string $email = 'julian_allen@papua.go.id',
        string $nip = '199507132020111001',
        string $password = 'papua'
    ): array {

        // Pastikan email dan NIP unik untuk setiap test case
        $user = $this->makeUser($email, $password);

        // Buat pegawai terkait dengan user yang baru dibuat
        $pegawai = Pegawai::create([
            'user_id' => $user->id,
            'nip' => $nip,
            'nama' => 'Pegawai',
            'jabatan' => 'Staff',
            'unit_kerja' => 'Dinas Komunikasi dan Informatika',
        ]);

        // Aktifkan autentikasi untuk user ini
        return [$user, $pegawai];
    }

    // Metode untuk mengaktifkan autentikasi sebagai pegawai yang dibuat dengan makeUserWithPegawai
    protected function actingAsPegawai(): array
    {
        [$user, $pegawai] = $this->makeUserWithPegawai();
        Sanctum::actingAs($user);
        return [$user, $pegawai];
    }

    // Metode untuk membuat data absensi dengan pegawai terkait, dengan data yang dapat disesuaikan
    protected function makeAbsensi(Pegawai $pegawai, array $data = []): Absensi
    {
        // Pastikan untuk menggabungkan data default dengan data yang disesuaikan untuk setiap test case
        return Absensi::create(array_merge([
            'pegawai_id' => $pegawai->id, 
            'tanggal' => '2026-05-21', 
            'jam_masuk' => '07:30:00',
            'jam_pulang' => null,
            'status_masuk' => 'tepat_waktu',
            'status_pulang' => null,
            'status' => 'absen_sekali',
            'keterangan' => null,
        ], $data));
    }
}
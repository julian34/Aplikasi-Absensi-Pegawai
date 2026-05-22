<?php

namespace App\Http\Controllers;

// Pastikan untuk mengimpor model dan kelas yang diperlukan
use Carbon\Carbon; // ​DateTime Handling dalam Laravel
use Illuminate\Http\Request; // Untuk menangani request dari client
use Illuminate\Support\Facades\DB; // Untuk transaksi database

// impor model Absensi dan Pegawai
use App\Models\Absensi;
use App\Models\Pegawai;

class AbsensiController extends Controller
{
    // fungsi untuk mendapatkan data absensi hari ini untuk pegawai yang login
    public function today(Request $request)
    {

        // ambil data pegawai berdasarkan user yang login
        $pegawai = $this->getPegawaiLogin($request);

        // jika data pegawai tidak ditemukan, kembalikan response error
        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai untuk user ini belum tersedia.',
            ], 404);
        }

        // ambil data absensi hari ini untuk pegawai yang login
        // $serverTime = Carbon::now('Asia/Jakarta')->addDays(2)->setTime(8, 0, 0);
        $serverTime = Carbon::now('Asia/Jakarta');
        $today = $serverTime->toDateString();

        // Cari absensi hari ini untuk pegawai yang login
        $absensi = Absensi::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        // jika data absensi tidak ditemukan, kembalikan response error
        return response()->json([
            'success' => true,
            'message' => 'Data absensi hari ini berhasil diambil.',
            'data' => $absensi,
            'server_time' => $serverTime->toIso8601String(),
            'is_weekend' => $serverTime->isWeekend(),
        ]);
    }

    // fungsi untuk melakukan absen datang
    public function absenDatang(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        // $now = Carbon::now('Asia/Jakarta')->addDays(2);
        $today = $now->toDateString();

        // absen datang hanya bisa dilakukan pada hari Senin sampai Jumat
        if (!$now->isWeekday()) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi hanya dapat dilakukan pada hari Senin sampai Jumat.',
            ], 422);
        }

        $pegawai = $this->getPegawaiLogin($request);

        // jika data pegawai tidak ditemukan, kembalikan response error
        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai untuk user ini belum tersedia.',
            ], 404);
        }

        // gunakan transaksi untuk memastikan data absensi tidak duplikat jika ada request bersamaan
        return DB::transaction(function () use ($pegawai, $now, $today) {
            $absensi = Absensi::where('pegawai_id', $pegawai->id)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->first();

            // jika sudah ada data absensi untuk hari ini dan sudah absen datang, kembalikan response error
            if ($absensi && $absensi->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen datang hari ini.',
                ], 422);
            }
            
            // Tentukan status masuk berdasarkan waktu absen
            $jamMasukNormal = Carbon::parse($today . ' 07:30:00', 'Asia/Jakarta');
            $batasToleransi = Carbon::parse($today . ' 07:45:00', 'Asia/Jakarta');

            // Default status masuk adalah tepat waktu
            $statusMasuk = 'tepat_waktu';
            $keterangan = 'Absen datang tepat waktu.';

            // Jika absen datang setelah batas toleransi, maka status masuk menjadi terlambat
            if ($now->greaterThan($batasToleransi)) {
                $statusMasuk = 'terlambat';
                $menitTerlambat = (int) round($jamMasukNormal->diffInRealMinutes($now));
                $keterangan = 'Terlambat ' . $this->formatMenit($menitTerlambat) . '.';
            }

            // jika belum ada data absensi untuk hari ini, buat data baru. Jika sudah ada tapi belum absen datang, update data tersebut
            $absensi = Absensi::create([
                'pegawai_id' => $pegawai->id,
                'tanggal' => $today,
                'jam_masuk' => $now->format('H:i:s'),
                'jam_pulang' => null,
                'status_masuk' => $statusMasuk,
                'status_pulang' => null,
                'status' => 'absen_sekali',
                'keterangan' => $keterangan,
            ]);

            // kembalikan response sukses dengan data absensi yang baru dibuat
            return response()->json([
                'success' => true,
                'message' => 'Absen datang berhasil.',
                'data' => $absensi,
            ], 201);
        });
    }

    // fungsi untuk melakukan absen pulang
    public function absenPulang(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        // absen pulang hanya bisa dilakukan pada hari Senin sampai Jumat
        if (!$now->isWeekday()) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi hanya dapat dilakukan pada hari Senin sampai Jumat.',
            ], 422);
        }

        // ambil data pegawai berdasarkan user yang login
        $pegawai = $this->getPegawaiLogin($request);

        // jika data pegawai tidak ditemukan, kembalikan response error
        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai untuk user ini belum tersedia.',
            ], 404);
        }

        // gunakan transaksi untuk memastikan data absensi tidak duplikat jika ada request bersamaan
        return DB::transaction(function () use ($pegawai, $now, $today) {
            // Cari data absensi hari ini untuk pegawai yang login dengan lock for update untuk
            $absensi = Absensi::where('pegawai_id', $pegawai->id)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->first();

            // jika belum ada data absensi atau belum absen datang, kembalikan response error
            if (!$absensi || !$absensi->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan absen datang hari ini.',
                ], 422);
            }

            // jika sudah absen pulang, kembalikan response error
            if ($absensi->jam_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen pulang hari ini.',
                ], 422);
            }

            // Tentukan status pulang berdasarkan waktu absen
            $jamPulangNormal = Carbon::parse($today . ' 16:00:00', 'Asia/Jakarta');

            // Default status pulang adalah sesuai jam
            $statusPulang = 'sesuai_jam';
            $keteranganPulang = 'Absen pulang sesuai jam kerja.';

            // Jika absen pulang sebelum jam pulang normal, maka status pulang menjadi pulang cepat
            if ($now->lessThan($jamPulangNormal)) {
                $statusPulang = 'pulang_cepat';
                $menitPulangCepat = (int) round($now->diffInRealMinutes($jamPulangNormal));
                $keteranganPulang = 'Pulang cepat ' . $this->formatMenit($menitPulangCepat) . '.';
            }

            // Tentukan status akhir berdasarkan status masuk dan status pulang
            $statusAkhir = 'hadir';

            // Jika status pulang adalah pulang cepat, maka status akhir menjadi pulang cepat
            if ($absensi->status_masuk === 'terlambat') {
                $statusAkhir = 'terlambat';
            }

            // Jika status pulang adalah pulang cepat, maka status akhir menjadi pulang cepat
            $keteranganLama = $absensi->keterangan ? $absensi->keterangan . ' ' : '';

            // Update data absensi dengan jam pulang, status pulang, status akhir, dan keterangan
            $absensi->update([
                'jam_pulang' => $now->format('H:i:s'),
                'status_pulang' => $statusPulang,
                'status' => $statusAkhir,
                'keterangan' => $keteranganLama . $keteranganPulang,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen pulang berhasil.',
                'data' => $absensi->fresh(),
            ]);
        });
    }

    // fungsi pembantu untuk mendapatkan data pegawai berdasarkan user yang login
    private function getPegawaiLogin(Request $request): ?Pegawai
    {
        // ambil data pegawai berdasarkan user yang login
        return Pegawai::where('user_id', $request->user()->id)->first();
    }

    // fungsi pembantu untuk format menit ke jam dan menits
    private function formatMenit(int $menit): string
    {
        // Jika menit kurang dari 60, cukup tampilkan menit
        if ($menit < 60) {
            return $menit . ' menit';
        }

        // Jika menit 60 atau lebih, konversi ke jam dan sisa menit
        $jam  = intdiv($menit, 60);
        $sisa = $menit % 60;

        // Format output
        if ($sisa === 0) {
            return $jam . ' jam';
        }

        // Jika ada sisa menit, tampilkan dalam format "X jam Y menit"
        return $jam . ' jam ' . $sisa . ' menit';
    }
}
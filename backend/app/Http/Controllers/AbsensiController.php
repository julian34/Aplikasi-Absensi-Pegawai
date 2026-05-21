<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function today(Request $request)
    {
        $pegawai = $this->getPegawaiLogin($request);

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai untuk user ini belum tersedia.',
            ], 404);
        }

        $today = Carbon::now('Asia/Jakarta')->toDateString();

        $absensi = Absensi::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Data absensi hari ini berhasil diambil.',
            'data' => $absensi,
        ]);
    }

    public function absenDatang(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        if (!$now->isWeekday()) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi hanya dapat dilakukan pada hari Senin sampai Jumat.',
            ], 422);
        }

        $pegawai = $this->getPegawaiLogin($request);

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai untuk user ini belum tersedia.',
            ], 404);
        }

        return DB::transaction(function () use ($pegawai, $now, $today) {
            $absensi = Absensi::where('pegawai_id', $pegawai->id)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->first();

            if ($absensi && $absensi->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen datang hari ini.',
                ], 422);
            }

            $jamMasukNormal = Carbon::parse($today . ' 07:30:00', 'Asia/Jakarta');
            $batasToleransi = Carbon::parse($today . ' 07:45:00', 'Asia/Jakarta');

            $statusMasuk = 'tepat_waktu';
            $keterangan = 'Absen datang tepat waktu.';

            if ($now->greaterThan($batasToleransi)) {
                $statusMasuk = 'terlambat';
                $menitTerlambat = $jamMasukNormal->diffInMinutes($now);
                $keterangan = 'Terlambat ' . $menitTerlambat . ' menit.';
            }

            $absensi = Absensi::create([
                'pegawai_id' => $pegawai->id,
                'tanggal' => $today,
                'jam_masuk' => $now->format('H:i:s'),
                'jam_pulang' => null,
                'status_masuk' => $statusMasuk,
                'status_pulang' => null,

                // Sesuai enum migration Anda
                // Setelah absen datang, status dibuat absen_sekali
                'status' => 'absen_sekali',

                'keterangan' => $keterangan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen datang berhasil.',
                'data' => $absensi,
            ], 201);
        });
    }

    public function absenPulang(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        if (!$now->isWeekday()) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi hanya dapat dilakukan pada hari Senin sampai Jumat.',
            ], 422);
        }

        $pegawai = $this->getPegawaiLogin($request);

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai untuk user ini belum tersedia.',
            ], 404);
        }

        return DB::transaction(function () use ($pegawai, $now, $today) {
            $absensi = Absensi::where('pegawai_id', $pegawai->id)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->first();

            if (!$absensi || !$absensi->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan absen datang hari ini.',
                ], 422);
            }

            if ($absensi->jam_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen pulang hari ini.',
                ], 422);
            }

            $jamPulangNormal = Carbon::parse($today . ' 16:00:00', 'Asia/Jakarta');

            $statusPulang = 'sesuai_jam';
            $keteranganPulang = 'Absen pulang sesuai jam kerja.';

            if ($now->lessThan($jamPulangNormal)) {
                $statusPulang = 'pulang_cepat';
                $menitPulangCepat = $now->diffInMinutes($jamPulangNormal);
                $keteranganPulang = 'Pulang cepat ' . $menitPulangCepat . ' menit.';
            }

            /*
             * Karena enum status Anda hanya:
             * absen_sekali, hadir, terlambat, tidak hadir
             *
             * Maka pulang cepat tidak dimasukkan ke kolom status.
             * Pulang cepat cukup disimpan di status_pulang.
             */
            $statusAkhir = 'hadir';

            if ($absensi->status_masuk === 'terlambat') {
                $statusAkhir = 'terlambat';
            }

            $keteranganLama = $absensi->keterangan ? $absensi->keterangan . ' ' : '';

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

    private function getPegawaiLogin(Request $request): ?Pegawai
    {
        return Pegawai::where('user_id', $request->user()->id)->first();
    }
}
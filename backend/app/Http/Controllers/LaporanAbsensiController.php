<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');

        $bulan = (int) $request->query('bulan', $now->month);
        $tahun = (int) $request->query('tahun', $now->year);

        if ($bulan < 1 || $bulan > 12 || $tahun < 2000 || $tahun > 2100) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter bulan atau tahun tidak valid.',
            ], 422);
        }

        $startDate = Carbon::createFromDate($tahun, $bulan, 1, 'Asia/Jakarta')->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();
        $daysInMonth = $endDate->day;

        $pegawaiList = Pegawai::with(['absensi' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);
        }])->orderBy('nama')->get();

        $result = $pegawaiList->map(function ($pegawai) use ($tahun, $bulan, $daysInMonth) {
            // Map day-of-month => absensi record
            $absensiMap = $pegawai->absensi->keyBy(function ($item) {
                return (int) Carbon::parse($item->tanggal)->day;
            });

            $harian = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date     = Carbon::createFromDate($tahun, $bulan, $day, 'Asia/Jakarta');
                $tanggalStr = $date->toDateString();

                if (!$date->isWeekday()) {
                    $harian[] = [
                        'tanggal'       => $tanggalStr,
                        'status'        => 'libur',
                        'jam_masuk'     => null,
                        'jam_pulang'    => null,
                        'status_masuk'  => null,
                        'status_pulang' => null,
                        'keterangan'    => null,
                    ];
                } elseif (isset($absensiMap[$day])) {
                    $a = $absensiMap[$day];

                    // Normalize 'tidak hadir' (with space from DB enum) to 'tidak_hadir'
                    $status = $a->status === 'tidak hadir' ? 'tidak_hadir' : $a->status;

                    $harian[] = [
                        'tanggal'       => $tanggalStr,
                        'status'        => $status,
                        'jam_masuk'     => $a->jam_masuk,
                        'jam_pulang'    => $a->jam_pulang,
                        'status_masuk'  => $a->status_masuk,
                        'status_pulang' => $a->status_pulang,
                        'keterangan'    => $a->keterangan,
                    ];
                } else {
                    $harian[] = [
                        'tanggal'       => $tanggalStr,
                        'status'        => 'tidak_hadir',
                        'jam_masuk'     => null,
                        'jam_pulang'    => null,
                        'status_masuk'  => null,
                        'status_pulang' => null,
                        'keterangan'    => null,
                    ];
                }
            }

            return [
                'pegawai_id' => $pegawai->id,
                'nama'       => $pegawai->nama,
                'nip'        => $pegawai->nip,
                'jabatan'    => $pegawai->jabatan,
                'unit_kerja' => $pegawai->unit_kerja,
                'harian'     => $harian,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'pegawai' => $result,
            ],
        ]);
    }
}

<?php

namespace Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class AbsensiUnitTest extends TestCase
{
    public function test_status_masuk_tepat_waktu_jika_absen_sebelum_atau_sama_dengan_0745(): void
    {
        $today = '2026-05-21';

        $now = Carbon::parse($today . ' 07:45:00', 'Asia/Jakarta');
        $batasToleransi = Carbon::parse($today . ' 07:45:00', 'Asia/Jakarta');

        $statusMasuk = $now->greaterThan($batasToleransi)
            ? 'terlambat'
            : 'tepat_waktu';

        $this->assertSame('tepat_waktu', $statusMasuk);
    }

    public function test_status_masuk_terlambat_jika_absen_setelah_0745(): void
    {
        $today = '2026-05-21';

        $now = Carbon::parse($today . ' 08:00:00', 'Asia/Jakarta');
        $batasToleransi = Carbon::parse($today . ' 07:45:00', 'Asia/Jakarta');

        $statusMasuk = $now->greaterThan($batasToleransi)
            ? 'terlambat'
            : 'tepat_waktu';

        $this->assertSame('terlambat', $statusMasuk);
    }

    public function test_status_pulang_sesuai_jam_jika_pulang_jam_1600_atau_lebih(): void
    {
        $today = '2026-05-21';

        $now = Carbon::parse($today . ' 16:00:00', 'Asia/Jakarta');
        $jamPulangNormal = Carbon::parse($today . ' 16:00:00', 'Asia/Jakarta');

        $statusPulang = $now->lessThan($jamPulangNormal)
            ? 'pulang_cepat'
            : 'sesuai_jam';

        $this->assertSame('sesuai_jam', $statusPulang);
    }

    public function test_status_pulang_cepat_jika_pulang_sebelum_1600(): void
    {
        $today = '2026-05-21';

        $now = Carbon::parse($today . ' 15:30:00', 'Asia/Jakarta');
        $jamPulangNormal = Carbon::parse($today . ' 16:00:00', 'Asia/Jakarta');

        $statusPulang = $now->lessThan($jamPulangNormal)
            ? 'pulang_cepat'
            : 'sesuai_jam';

        $this->assertSame('pulang_cepat', $statusPulang);
    }

    public function test_status_akhir_hadir_jika_masuk_tepat_waktu(): void
    {
        $statusMasuk = 'tepat_waktu';

        $statusAkhir = $statusMasuk === 'terlambat'
            ? 'terlambat'
            : 'hadir';

        $this->assertSame('hadir', $statusAkhir);
    }

    public function test_status_akhir_terlambat_jika_status_masuk_terlambat(): void
    {
        $statusMasuk = 'terlambat';

        $statusAkhir = $statusMasuk === 'terlambat'
            ? 'terlambat'
            : 'hadir';

        $this->assertSame('terlambat', $statusAkhir);
    }
}
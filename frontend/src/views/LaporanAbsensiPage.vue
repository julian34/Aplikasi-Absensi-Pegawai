<template>
  <main class="laporan-page">
    <section class="laporan-card">
      <div class="laporan-header">
        <div>
          <h1>Laporan Absensi</h1>
          <p>Detail absensi pegawai berdasarkan bulan dan tahun</p>
        </div>

        <button class="btn-refresh" @click="loadLaporan">Refresh</button>
      </div>

      <div class="filter-box">
        <div class="filter-group">
          <label>Bulan</label>
          <select v-model="selectedBulan" @change="loadLaporan">
            <option
              v-for="bulan in bulanList"
              :key="bulan.value"
              :value="bulan.value"
            >
              {{ bulan.label }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label>Tahun</label>
          <select v-model="selectedTahun" @change="loadLaporan">
            <option v-for="tahun in tahunList" :key="tahun" :value="tahun">
              {{ tahun }}
            </option>
          </select>
        </div>
      </div>

      <div class="legend-box">
        <span><i class="dot hadir"></i> Hadir</span>
        <span><i class="dot terlambat"></i> Terlambat</span>
        <span><i class="dot absen-sekali"></i> Absen Sekali</span>
        <span><i class="dot tidak-hadir"></i> Tidak Hadir</span>
        <span><i class="dot libur"></i> Libur</span>
      </div>

      <div v-if="loading" class="loading-box">Memuat laporan absensi...</div>

      <div v-else class="table-wrapper">
        <table class="laporan-table">
          <thead>
            <tr>
              <th class="sticky-col pegawai-col">Pegawai</th>
              <th v-for="hari in jumlahHari" :key="hari" class="tanggal-col">
                {{ hari }}
              </th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="pegawai in laporan" :key="pegawai.pegawai_id">
              <td class="sticky-col pegawai-col">
                <div class="pegawai-name">{{ pegawai.nama }}</div>
                <div class="pegawai-meta">{{ pegawai.nip }}</div>
                <div class="pegawai-meta">{{ pegawai.jabatan || "-" }}</div>
              </td>

              <td
                v-for="item in pegawai.harian"
                :key="item.tanggal"
                class="tanggal-cell"
                @click="openDetail(pegawai, item)"
              >
                <span
                  class="status-pill"
                  :class="getStatusClass(item.status)"
                  :title="getStatusLabel(item.status)"
                >
                  {{ getStatusInitial(item.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="!loading && laporan.length === 0" class="empty-box">
        Data laporan absensi belum tersedia.
      </div>
    </section>

    <div v-if="selectedDetail" class="modal-overlay" @click.self="closeDetail">
      <section class="detail-modal">
        <div class="modal-header">
          <div>
            <h2>Detail Absensi</h2>
            <p>
              {{ selectedDetail.pegawai.nama }} -
              {{ formatTanggal(selectedDetail.absensi.tanggal) }}
            </p>
          </div>

          <button class="btn-close" @click="closeDetail">×</button>
        </div>

        <div class="detail-grid">
          <div>
            <label>NIP</label>
            <strong>{{ selectedDetail.pegawai.nip }}</strong>
          </div>

          <div>
            <label>Jabatan</label>
            <strong>{{ selectedDetail.pegawai.jabatan || "-" }}</strong>
          </div>

          <div>
            <label>Jam Masuk</label>
            <strong>{{ selectedDetail.absensi.jam_masuk || "-" }}</strong>
          </div>

          <div>
            <label>Jam Pulang</label>
            <strong>{{ selectedDetail.absensi.jam_pulang || "-" }}</strong>
          </div>

          <div>
            <label>Status Masuk</label>
            <strong>{{
              formatStatus(selectedDetail.absensi.status_masuk)
            }}</strong>
          </div>

          <div>
            <label>Status Pulang</label>
            <strong>{{
              formatStatus(selectedDetail.absensi.status_pulang)
            }}</strong>
          </div>

          <div>
            <label>Status Akhir</label>
            <strong>{{ formatStatus(selectedDetail.absensi.status) }}</strong>
          </div>

          <div>
            <label>Keterangan</label>
            <strong>{{ selectedDetail.absensi.keterangan || "-" }}</strong>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { laporanAbsensiService } from "@/services/laporanAbsensiService";
import "@/assets/laporanAbsensi.css";

const now = new Date();

const selectedBulan = ref(now.getMonth() + 1);
const selectedTahun = ref(now.getFullYear());

const jumlahHari = ref(0);
const laporan = ref([]);
const loading = ref(false);
const selectedDetail = ref(null);

const bulanList = [
  { value: 1, label: "Januari" },
  { value: 2, label: "Februari" },
  { value: 3, label: "Maret" },
  { value: 4, label: "April" },
  { value: 5, label: "Mei" },
  { value: 6, label: "Juni" },
  { value: 7, label: "Juli" },
  { value: 8, label: "Agustus" },
  { value: 9, label: "September" },
  { value: 10, label: "Oktober" },
  { value: 11, label: "November" },
  { value: 12, label: "Desember" },
];

const tahunList = computed(() => {
  const currentYear = now.getFullYear();
  return [currentYear - 1, currentYear, currentYear + 1];
});

const loadLaporan = async () => {
  loading.value = true;

  try {
    const result = await laporanAbsensiService.getLaporanAbsensi(
      selectedBulan.value,
      selectedTahun.value,
    );

    jumlahHari.value = result.data.jumlah_hari;
    laporan.value = result.data.pegawai;
  } catch (error) {
    console.error(error);
    laporan.value = [];
    jumlahHari.value = 0;
  } finally {
    loading.value = false;
  }
};

const openDetail = (pegawai, absensi) => {
  selectedDetail.value = {
    pegawai,
    absensi,
  };
};

const closeDetail = () => {
  selectedDetail.value = null;
};

const getStatusClass = (status) => {
  if (status === "hadir") return "status-hadir";
  if (status === "terlambat") return "status-terlambat";
  if (status === "absen_sekali") return "status-absen-sekali";
  if (status === "libur") return "status-libur";
  return "status-tidak-hadir";
};

const getStatusInitial = (status) => {
  if (status === "hadir") return "H";
  if (status === "terlambat") return "T";
  if (status === "absen_sekali") return "A";
  if (status === "libur") return "-";
  return "X";
};

const getStatusLabel = (status) => {
  if (status === "hadir") return "Hadir";
  if (status === "terlambat") return "Terlambat";
  if (status === "absen_sekali") return "Absen Sekali";
  if (status === "libur") return "Libur";
  return "Tidak Hadir";
};

const formatStatus = (value) => {
  if (!value) return "-";

  return value
    .replaceAll("_", " ")
    .replace(/\b\w/g, (char) => char.toUpperCase());
};

const formatTanggal = (tanggal) => {
  return new Date(tanggal).toLocaleDateString("id-ID", {
    weekday: "long",
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
};

onMounted(() => {
  loadLaporan();
});
</script>

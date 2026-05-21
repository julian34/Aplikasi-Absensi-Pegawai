<template>
  <main class="laporan-page">
    <section class="laporan-headbar">
      <div>
        <h1>Laporan Absensi</h1>
        <p>Detail absensi pegawai berdasarkan bulan dan tahun</p>
      </div>

      <div class="laporan-headbar-actions">
        <button class="btn-dashboard" @click="goToDashboard">Dashboard</button>

        <button class="btn-refresh" @click="loadLaporan">Refresh</button>

        <button class="btn-logout" @click="handleLogout">Logout</button>
      </div>
    </section>

    <section class="laporan-card">
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

              <th v-for="hari in daysInMonth" :key="hari" class="tanggal-col">
                {{ hari }}
              </th>
            </tr>
          </thead>

          <tbody>
            <tr v-if="laporan.length === 0">
              <td :colspan="daysInMonth + 1" class="empty-row">
                Data laporan absensi belum tersedia.
              </td>
            </tr>

            <tr v-for="pegawai in laporan" :key="pegawai.pegawai_id">
              <td class="sticky-col pegawai-col">
                <div class="pegawai-name">{{ pegawai.nama }}</div>
                <div class="pegawai-meta">NIP: {{ pegawai.nip || "-" }}</div>
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
    </section>

    <div v-if="selectedDetail" class="modal-overlay" @click.self="closeDetail">
      <section class="detail-modal">
        <div class="modal-header">
          <div>
            <h2>Detail Absensi</h2>
            <p>
              {{ selectedDetail.pegawai.nama }}
              -
              {{ formatTanggal(selectedDetail.absensi.tanggal) }}
            </p>
          </div>

          <button class="btn-close" @click="closeDetail">×</button>
        </div>

        <div class="detail-grid">
          <div>
            <label>NIP</label>
            <strong>{{ selectedDetail.pegawai.nip || "-" }}</strong>
          </div>

          <div>
            <label>Jabatan</label>
            <strong>{{ selectedDetail.pegawai.jabatan || "-" }}</strong>
          </div>

          <div>
            <label>Unit Kerja</label>
            <strong>{{ selectedDetail.pegawai.unit_kerja || "-" }}</strong>
          </div>

          <div>
            <label>Tanggal</label>
            <strong>{{ selectedDetail.absensi.tanggal }}</strong>
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
            <strong>{{
              formatKeterangan(selectedDetail.absensi.keterangan)
            }}</strong>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";
import { laporanAbsensiService } from "@/services/laporanAbsensiService";
import "@/assets/laporanAbsensi.css";

const router = useRouter();
const { logout } = useAuth();

const now = new Date();

const selectedBulan = ref(now.getMonth() + 1);
const selectedTahun = ref(now.getFullYear());

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

const daysInMonth = computed(() => {
  return new Date(selectedTahun.value, selectedBulan.value, 0).getDate();
});

const loadLaporan = async () => {
  loading.value = true;

  try {
    const result = await laporanAbsensiService.getLaporanAbsensi(
      selectedBulan.value,
      selectedTahun.value,
    );

    laporan.value = result.data?.pegawai || [];
  } catch (error) {
    console.error("Gagal memuat laporan absensi:", error);
    laporan.value = [];
  } finally {
    loading.value = false;
  }
};

const goToDashboard = () => {
  router.push("/dashboard");
};

const handleLogout = async () => {
  await logout();
  router.push("/login");
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

const formatKeterangan = (keterangan) => {
  if (!keterangan) return "-";

  return keterangan.replace(/(\d+(?:\.\d+)?)\s*menit/g, (_, p1) => {
    const totalMenit = Math.round(parseFloat(p1));
    if (totalMenit < 60) return totalMenit + " menit";
    const jam = Math.floor(totalMenit / 60);
    const sisa = totalMenit % 60;
    return sisa === 0 ? jam + " jam" : jam + " jam " + sisa + " menit";
  });
};

const formatTanggal = (tanggal) => {
  if (!tanggal) return "-";

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

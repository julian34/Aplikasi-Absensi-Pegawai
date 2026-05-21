<template>
  <div class="dashboard-container">
    <div class="dashboard-header">
      <div>
        <h1>Dashboard - Aplikasi Absensi Pegawai</h1>
        <p class="dashboard-subtitle">
          Kelola absensi datang dan pulang pegawai
        </p>
      </div>
      <div class="dashboard-actions">
        <button @click="goToLaporanAbsensi" class="btn-report">
          Laporan Absensi
        </button>

        <button @click="handleLogout" class="btn-logout">Logout</button>
      </div>
    </div>

    <div v-if="user" class="dashboard-content">
      <div class="user-info-card">
        <h2>Informasi User</h2>

        <div class="info-group">
          <label>Nama:</label>
          <p>{{ user.name }}</p>
        </div>

        <div class="info-group">
          <label>Email:</label>
          <p>{{ user.email }}</p>
        </div>
      </div>

      <div v-if="user.pegawai" class="pegawai-info-card">
        <h2>Informasi Pegawai</h2>

        <div class="info-group">
          <label>NIP:</label>
          <p>{{ user.pegawai.nip }}</p>
        </div>

        <div class="info-group">
          <label>Nama:</label>
          <p>{{ user.pegawai.nama }}</p>
        </div>

        <div class="info-group">
          <label>Jabatan:</label>
          <p>{{ user.pegawai.jabatan }}</p>
        </div>

        <div class="info-group">
          <label>Unit Kerja:</label>
          <p>{{ user.pegawai.unit_kerja }}</p>
        </div>
      </div>

      <div class="attendance-card">
        <div class="attendance-header">
          <div>
            <h2>Absensi Hari Ini</h2>
            <p>{{ todayText }}</p>
          </div>

          <span class="attendance-badge" :class="statusClass">
            {{ statusText }}
          </span>
        </div>

        <div v-if="attendanceLoading" class="attendance-loading">
          Memuat data absensi...
        </div>

        <div v-else>
          <div class="attendance-grid">
            <div class="attendance-item">
              <label>Jam Masuk</label>
              <strong>{{ absensi?.jam_masuk || "-" }}</strong>
            </div>

            <div class="attendance-item">
              <label>Jam Pulang</label>
              <strong>{{ absensi?.jam_pulang || "-" }}</strong>
            </div>

            <div class="attendance-item">
              <label>Status Masuk</label>
              <strong>{{ formatStatus(absensi?.status_masuk) }}</strong>
            </div>

            <div class="attendance-item">
              <label>Status Pulang</label>
              <strong>{{ formatStatus(absensi?.status_pulang) }}</strong>
            </div>
          </div>

          <div class="attendance-actions">
            <button
              class="btn-attendance btn-check-in"
              :disabled="!canAbsenDatang || actionLoading"
              @click="handleAbsenDatang"
            >
              {{ actionLoading ? "Memproses..." : "Absen Datang" }}
            </button>

            <button
              class="btn-attendance btn-check-out"
              :disabled="!canAbsenPulang || actionLoading"
              @click="handleAbsenPulang"
            >
              {{ actionLoading ? "Memproses..." : "Absen Pulang" }}
            </button>
          </div>

          <div v-if="message" class="message-box" :class="messageType">
            {{ message }}
          </div>

          <div class="attendance-note">
            <strong>Keterangan:</strong>
            <span>{{
              formatKeterangan(absensi?.keterangan)
            }}</span>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="loading">
      <p>Loading...</p>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";
import { absensiService } from "@/services/absensiService";
import "@/assets/dashboard.css";

const router = useRouter();

const { user, fetchUser, logout } = useAuth();

const absensi = ref(null);
const attendanceLoading = ref(false);
const actionLoading = ref(false);
const message = ref("");
const messageType = ref("success");

const todayText = computed(() => {
  return new Date().toLocaleDateString("id-ID", {
    weekday: "long",
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
});

const goToLaporanAbsensi = () => {
  router.push("/laporan-absensi");
};

const canAbsenDatang = computed(() => {
  return !absensi.value || !absensi.value.jam_masuk;
});

const canAbsenPulang = computed(() => {
  return absensi.value?.jam_masuk && !absensi.value?.jam_pulang;
});

const statusText = computed(() => {
  if (!absensi.value) {
    return "Belum Absen";
  }

  if (absensi.value.status === "absen_sekali") {
    return "Sudah Absen Datang";
  }

  if (absensi.value.status === "hadir") {
    return "Hadir";
  }

  if (absensi.value.status === "terlambat") {
    return "Terlambat";
  }

  if (absensi.value.status === "tidak hadir") {
    return "Tidak Hadir";
  }

  return formatStatus(absensi.value.status);
});

const statusClass = computed(() => {
  if (!absensi.value) {
    return "badge-empty";
  }

  if (absensi.value.status === "hadir") {
    return "badge-success";
  }

  if (absensi.value.status === "terlambat") {
    return "badge-warning";
  }

  if (absensi.value.status === "absen_sekali") {
    return "badge-info";
  }

  return "badge-danger";
});

const loadTodayAbsensi = async () => {
  attendanceLoading.value = true;
  message.value = "";

  try {
    const result = await absensiService.getToday();
    absensi.value = result.data;
  } catch (error) {
    showError(error);
  } finally {
    attendanceLoading.value = false;
  }
};

const handleAbsenDatang = async () => {
  actionLoading.value = true;
  message.value = "";

  try {
    const result = await absensiService.absenDatang();
    absensi.value = result.data;
    showSuccess(result.message || "Absen datang berhasil.");
  } catch (error) {
    showError(error);
  } finally {
    actionLoading.value = false;
  }
};

const handleAbsenPulang = async () => {
  actionLoading.value = true;
  message.value = "";

  try {
    const result = await absensiService.absenPulang();
    absensi.value = result.data;
    showSuccess(result.message || "Absen pulang berhasil.");
  } catch (error) {
    showError(error);
  } finally {
    actionLoading.value = false;
  }
};

const handleLogout = async () => {
  await logout();
  router.push("/login");
};

const formatStatus = (value) => {
  if (!value) return "-";

  return value
    .replaceAll("_", " ")
    .replace(/\b\w/g, (char) => char.toUpperCase());
};

const formatKeterangan = (keterangan) => {
  if (!keterangan) return "Belum ada keterangan absensi.";

  return keterangan.replace(/(\d+(?:\.\d+)?)\s*menit/g, (_, p1) => {
    const totalMenit = Math.round(parseFloat(p1));
    if (totalMenit < 60) return totalMenit + " menit";
    const jam = Math.floor(totalMenit / 60);
    const sisa = totalMenit % 60;
    return sisa === 0 ? jam + " jam" : jam + " jam " + sisa + " menit";
  });
};

const showSuccess = (text) => {
  message.value = text;
  messageType.value = "success";
};

const showError = (error) => {
  messageType.value = "error";

  if (error.response?.data?.message) {
    message.value = error.response.data.message;
    return;
  }

  message.value = "Terjadi kesalahan saat memproses absensi.";
};

onMounted(async () => {
  if (!user.value) {
    await fetchUser();
  }

  await loadTodayAbsensi();
});
</script>

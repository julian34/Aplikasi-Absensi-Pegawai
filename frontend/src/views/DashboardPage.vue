<template>
  <div class="dashboard-container">
    <div class="dashboard-header">
      <h1>Dashboard - Aplikasi Absensi Pegawai</h1>
      <button @click="handleLogout" class="btn-logout">Logout</button>
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
    </div>

    <div v-else class="loading">
      <p>Loading...</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";

const router = useRouter();
const { user, fetchUser, logout } = useAuth();

onMounted(async () => {
  // Fetch user data if not already loaded
  if (!user.value) {
    await fetchUser();
  }
});

const handleLogout = async () => {
  await logout();
  router.push("/login");
};
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  padding: 40px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
  background: white;
  padding: 20px 30px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dashboard-header h1 {
  margin: 0;
  color: #333;
  font-size: 28px;
}

.btn-logout {
  padding: 10px 20px;
  background: #ff6b6b;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-logout:hover {
  background: #ff5252;
}

.dashboard-content {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

.user-info-card,
.pegawai-info-card {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.user-info-card h2,
.pegawai-info-card h2 {
  margin-top: 0;
  margin-bottom: 20px;
  color: #333;
  font-size: 20px;
  border-bottom: 2px solid #667eea;
  padding-bottom: 10px;
}

.info-group {
  margin-bottom: 15px;
}

.info-group label {
  display: block;
  font-weight: 600;
  color: #667eea;
  margin-bottom: 5px;
  font-size: 14px;
}

.info-group p {
  margin: 0;
  color: #333;
  font-size: 16px;
  padding: 8px;
  background: #f8f9fa;
  border-radius: 6px;
}

.loading {
  text-align: center;
  color: white;
  font-size: 18px;
}

@media (max-width: 768px) {
  .dashboard-content {
    grid-template-columns: 1fr;
  }

  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }

  .dashboard-header h1 {
    font-size: 20px;
  }
}
</style>

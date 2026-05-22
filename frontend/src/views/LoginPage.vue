<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>Aplikasi Absensi Pegawai</h1>
        <p>Silakan login dengan Email atau NIP</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="login">Email atau NIP</label>
          <input
            id="login"
            v-model="formData.login"
            type="text"
            placeholder="Contoh: Andimultimedia@papua.go.id atau 19900101001"
            class="form-input"
            @blur="validateField('login')"
          />
          <span v-if="errors.login" class="error-message">{{
            errors.login
          }}</span>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="formData.password"
            type="password"
            placeholder="Masukkan password"
            class="form-input"
          />
          <span v-if="errors.password" class="error-message">{{
            errors.password
          }}</span>
        </div>

        <div v-if="error" class="alert alert-error">{{ error }}</div>

        <button
          type="submit"
          class="btn-login"
          :disabled="isLoading || !isFormValid"
        >
          <span v-if="isLoading">Loading...</span>
          <span v-else>Login</span>
        </button>
      </form>

      <div class="login-footer">
        <p>
          Apabila lupa password silahkan Menghubungi <b>Opertor Absen</b> pada
          dinas masing-masing
        </p>
        <p class="example">
          Perubahan data :
          <code>
            silahkan datang langsung ke dinas Komunikasi dan Informatika
            provinsi papua dengan membawa bukti dan surat pengantar dari dinas
            masing-masing
          </code>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";
import "@/assets/auth/login.css";

const router = useRouter();
const { login, isLoading, error } = useAuth();

const formData = ref({
  login: "",
  password: "",
});

const errors = ref({
  login: "",
  password: "",
});

const isFormValid = computed(() => {
  return (
    formData.value.login.trim() !== "" && formData.value.password.trim() !== ""
  );
});

const validateField = (field) => {
  if (field === "login") {
    const loginValue = formData.value.login.trim();
    const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(loginValue);
    const isNip = /^\d{11,18}$/.test(loginValue);

    if (!loginValue) {
      errors.value.login = "Email atau NIP harus diisi";
    } else if (!isEmail && !isNip) {
      errors.value.login = "Format email atau NIP tidak valid";
    } else {
      errors.value.login = "";
    }
  }
};

const handleLogin = async () => {
  // Validate before submit
  validateField("login");

  if (!isFormValid.value) {
    return;
  }

  const result = await login(formData.value.login, formData.value.password);

  if (result.success) {
    // Redirect to dashboard on successful login
    router.push("/dashboard");
  }
};
</script>

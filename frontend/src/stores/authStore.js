import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from "@/services/axiosService";

export const useAuthStore = defineStore("auth", () => {
  // State
  const user = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // Computed
  const isAuthenticated = computed(() => user.value !== null);

  // Actions
  const getCsrfToken = async () => {
    try {
      await axios.get("/sanctum/csrf-cookie");
    } catch (err) {
      console.error("Error fetching CSRF token:", err);
    }
  };

  const login = async (login, password) => {
    loading.value = true;
    error.value = null;

    try {
      // Get CSRF token first
      await getCsrfToken();

      const response = await axios.post("/login", {
        login, // Accepts both email and NIP
        password,
      });

      user.value = response.data.user;
      return {
        success: true,
        user: response.data.user,
      };
    } catch (err) {
      const errorMessage =
        err.response?.data?.message ||
        err.response?.data?.errors?.login?.[0] ||
        err.response?.data?.errors?.password?.[0] ||
        "Login gagal. Silakan coba lagi.";
      error.value = errorMessage;
      return {
        success: false,
        message: errorMessage,
      };
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    loading.value = true;
    error.value = null;

    try {
      await axios.post("/logout");
      user.value = null;
      return {
        success: true,
        message: "Logout berhasil",
      };
    } catch (err) {
      error.value = "Logout gagal";
      return {
        success: false,
        message: "Logout gagal",
      };
    } finally {
      loading.value = false;
    }
  };

  const fetchUser = async () => {
    try {
      const response = await axios.get("/user");
      user.value = response.data.user;
      return {
        success: true,
        user: response.data.user,
      };
    } catch (err) {
      user.value = null;
      return {
        success: false,
        message: "Gagal mengambil data user",
      };
    }
  };

  const clearAuth = () => {
    user.value = null;
    error.value = null;
  };

  return {
    // State
    user,
    loading,
    error,

    // Computed
    isAuthenticated,

    // Actions
    getCsrfToken,
    login,
    logout,
    fetchUser,
    clearAuth,
  };
});

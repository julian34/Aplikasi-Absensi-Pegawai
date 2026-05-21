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
  const login = async (login, password) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post("/login", {
        login, // Accepts both email and NIP
        password,
      });

      localStorage.setItem("auth_token", response.data.token);
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

    try {
      await axios.post("/logout");
    } catch {
      // API call may fail (e.g. token already expired), but we always clear local state
    } finally {
      localStorage.removeItem("auth_token");
      user.value = null;
      error.value = null;
      loading.value = false;
    }

    return { success: true, message: "Logout berhasil" };
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
      // Token tidak valid / expired — hapus agar router guard tidak retry terus
      if (err.response?.status === 401) {
        localStorage.removeItem("auth_token");
      }
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
    login,
    logout,
    fetchUser,
    clearAuth,
  };
});

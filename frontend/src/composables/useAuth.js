import { useAuthStore } from "@/stores/authStore";

export const useAuth = () => {
  const authStore = useAuthStore();

  return {
    // State
    user: authStore.user,
    isLoading: authStore.loading,
    error: authStore.error,

    // Computed
    isAuthenticated: authStore.isAuthenticated,

    // Actions
    login: authStore.login,
    logout: authStore.logout,
    fetchUser: authStore.fetchUser,
    clearAuth: authStore.clearAuth,
  };
};

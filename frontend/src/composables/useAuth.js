import { storeToRefs } from "pinia";
import { useAuthStore } from "@/stores/authStore";

export const useAuth = () => {
  const authStore = useAuthStore();
  const { user, loading: isLoading, error, isAuthenticated } = storeToRefs(authStore);

  return {
    // Reactive state (storeToRefs agar update store terlihat di komponen)
    user,
    isLoading,
    error,
    isAuthenticated,

    // Actions (tidak perlu storeToRefs untuk fungsi)
    login: authStore.login,
    logout: authStore.logout,
    fetchUser: authStore.fetchUser,
    clearAuth: authStore.clearAuth,
  };
};

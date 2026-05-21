import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import LoginPage from "@/views/LoginPage.vue";

// pegawai routes
import DashboardPage from "@/views/DashboardPage.vue";
import LaporanAbsensiPage from "@/views/LaporanAbsensiPage.vue";

const routes = [
  {
    path: "/login",
    name: "Login",
    component: LoginPage,
    meta: { requiresAuth: false },
  },
  {
    path: "/dashboard",
    name: "Dashboard",
    component: DashboardPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/",
    name: "Home",
    redirect: "/login",
  },

  // pegawai routes
  {
    path: "/laporan-absensi",
    name: "laporan-absensi",
    component: LaporanAbsensiPage,
    meta: {
      requiresAuth: true,
    },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Restore session from stored token on page reload
  if (!authStore.isAuthenticated && localStorage.getItem("auth_token")) {
    await authStore.fetchUser();
  }

  // If route requires auth and user not authenticated, redirect to login
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/login");
  }
  // If user is authenticated and trying to access login, redirect to dashboard
  else if (to.path === "/login" && authStore.isAuthenticated) {
    next("/dashboard");
  }
  // Otherwise proceed normally
  else {
    next();
  }
});

export default router;

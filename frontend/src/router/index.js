import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import LoginPage from "@/views/LoginPage.vue";
import DashboardPage from "@/views/DashboardPage.vue";

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
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

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

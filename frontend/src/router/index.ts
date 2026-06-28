import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/features/auth/pages/LoginPage.vue'),
      meta: { guest: true },
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('@/pages/admin/DashboardPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/',
      redirect: '/admin',
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (!auth.initialized) {
    await auth.init()
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'login' }
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'admin' }
  }
})

export default router

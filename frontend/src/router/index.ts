import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    // Public
    {
      path: '/products',
      name: 'products',
      component: () => import('@/features/products/pages/ProductListPage.vue'),
    },
    {
      path: '/products/:id',
      name: 'product-detail',
      component: () => import('@/features/products/pages/ProductDetailPage.vue'),
    },

    // Auth
    {
      path: '/login',
      name: 'login',
      component: () => import('@/features/auth/pages/LoginPage.vue'),
      meta: { guest: true },
    },

    // Admin
    {
      path: '/admin',
      name: 'admin',
      component: () => import('@/pages/admin/DashboardPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/products',
      name: 'admin-products',
      component: () => import('@/features/products/pages/admin/AdminProductsPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/products/create',
      name: 'admin-products-create',
      component: () => import('@/features/products/pages/admin/CreateProductPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/products/:id/edit',
      name: 'admin-products-edit',
      component: () => import('@/features/products/pages/admin/EditProductPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },

    {
      path: '/',
      redirect: '/products',
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

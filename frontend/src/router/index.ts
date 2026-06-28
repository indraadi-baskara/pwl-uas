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

    // Cart + Checkout + Orders (buyer only — admins are redirected to /admin)
    {
      path: '/cart',
      name: 'cart',
      component: () => import('@/features/cart/pages/CartPage.vue'),
      meta: { requiresAuth: true, buyerOnly: true },
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: () => import('@/features/orders/pages/CheckoutPage.vue'),
      meta: { requiresAuth: true, buyerOnly: true },
    },
    {
      path: '/orders',
      name: 'orders',
      component: () => import('@/features/orders/pages/OrdersPage.vue'),
      meta: { requiresAuth: true, buyerOnly: true },
    },
    {
      path: '/orders/:id',
      name: 'order-detail',
      component: () => import('@/features/orders/pages/OrderDetailPage.vue'),
      meta: { requiresAuth: true, buyerOnly: true },
    },

    // Auth
    {
      path: '/login',
      name: 'login',
      component: () => import('@/features/auth/pages/LoginPage.vue'),
      meta: { guest: true },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/features/auth/pages/RegisterPage.vue'),
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
      path: '/admin/orders',
      name: 'admin-orders',
      component: () => import('@/features/orders/pages/admin/AdminOrdersPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/orders/:id',
      name: 'admin-order-detail',
      component: () => import('@/features/orders/pages/admin/AdminOrderDetailPage.vue'),
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

  if (to.meta.buyerOnly && auth.isAdmin) {
    return { name: 'admin' }
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: auth.isAdmin ? 'admin' : 'products' }
  }
})

export default router

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { postLogout } from '@/features/auth'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useCart } from '@/features/cart/api/use-cart'

const router    = useRouter()
const auth      = useAuthStore()
const cartStore = useCartStore()

useCart()

async function logout() {
  await postLogout()
  auth.clearUser()
  void router.push({ name: 'login' })
}
</script>

<template>
  <header class="border-b border-surface bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
      <button
        class="text-left"
        @click="router.push({ name: 'products' })"
      >
        <p class="text-xl font-bold text-ink" style="font-family: var(--font-display)">
          Depo Waroeng Ban
        </p>
        <p class="text-xs text-ink-muted">Katalog Ban Lengkap</p>
      </button>

      <div class="flex items-center gap-3">
        <!-- Cart icon + badge (buyer only) -->
        <button
          v-if="auth.isAuthenticated && !auth.isAdmin"
          class="relative p-2 text-ink-muted transition-colors duration-160 hover:text-accent"
          title="Keranjang"
          @click="router.push({ name: 'cart' })"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
            />
          </svg>
          <span
            v-if="cartStore.itemCount > 0"
            class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center
                   rounded-full bg-accent text-[10px] font-bold text-white"
          >
            {{ cartStore.itemCount > 9 ? '9+' : cartStore.itemCount }}
          </span>
        </button>

        <!-- Orders link (buyer only) -->
        <button
          v-if="auth.isAuthenticated && !auth.isAdmin"
          class="text-sm text-ink-muted transition-colors duration-160 hover:text-ink"
          @click="router.push({ name: 'orders' })"
        >
          Pesanan
        </button>

        <!-- Admin link (admin only) -->
        <button
          v-if="auth.isAdmin"
          class="text-sm text-ink-muted transition-colors duration-160 hover:text-ink"
          @click="router.push({ name: 'admin' })"
        >
          Admin
        </button>

        <!-- Logout (authenticated) -->
        <button
          v-if="auth.isAuthenticated"
          class="rounded border border-surface px-3 py-1.5 text-sm text-ink-muted
                 transition-colors duration-160 hover:border-accent hover:text-accent"
          @click="logout"
        >
          Keluar
        </button>

        <!-- Login + Register (guest) -->
        <template v-else>
          <button
            class="text-sm text-ink-muted transition-colors duration-160 hover:text-ink"
            @click="router.push({ name: 'register' })"
          >
            Daftar
          </button>
          <button
            class="rounded border border-surface px-3 py-1.5 text-sm text-ink-muted
                   transition-colors duration-160 hover:border-ink hover:text-ink"
            @click="router.push({ name: 'login' })"
          >
            Masuk
          </button>
        </template>
      </div>
    </div>
  </header>
</template>

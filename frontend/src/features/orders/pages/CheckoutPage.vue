<script setup lang="ts">
import { useRouter } from 'vue-router'
import PublicHeader from '@/components/PublicHeader.vue'
import { useCart }       from '@/features/cart/api/use-cart'
import { usePlaceOrder } from '@/features/orders/api/use-place-order'

const router = useRouter()
const { data: cart, isLoading }    = useCart()
const { mutate, isPending, error } = usePlaceOrder()

function formatPrice(n: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
  }).format(n)
}

function confirmOrder() {
  mutate(undefined, {
    onSuccess: (order) => { void router.push({ name: 'order-detail', params: { id: order.id } }) },
  })
}
</script>

<template>
  <div class="min-h-screen bg-canvas">
    <PublicHeader />

    <main class="mx-auto max-w-2xl px-6 py-8">
      <button
        class="mb-4 flex items-center gap-1 text-sm text-ink-muted transition-colors hover:text-ink"
        @click="router.push({ name: 'cart' })"
      >
        ← Kembali ke keranjang
      </button>

      <h1 class="text-2xl font-bold text-ink" style="font-family: var(--font-display)">
        Konfirmasi Pesanan
      </h1>

      <!-- Loading -->
      <div v-if="isLoading" class="mt-6 animate-pulse space-y-3">
        <div v-for="n in 3" :key="n" class="h-12 rounded bg-surface" />
      </div>

      <!-- Empty cart guard -->
      <div
        v-else-if="!cart?.items.length"
        class="mt-8 rounded-xl border border-surface bg-white px-6 py-12 text-center"
      >
        <p class="text-ink-muted">Keranjang kamu kosong.</p>
        <button
          class="mt-4 text-sm text-accent underline"
          @click="router.push({ name: 'products' })"
        >
          Ke katalog
        </button>
      </div>

      <div v-else class="mt-6 rounded-xl border border-surface bg-white p-6">
        <!-- Error -->
        <p v-if="error" class="mb-4 rounded bg-accent-soft px-4 py-2 text-sm text-accent">
          {{ (error as Error).message }}
        </p>

        <!-- Item list -->
        <div class="divide-y divide-surface">
          <div
            v-for="item in cart.items"
            :key="item.id"
            class="flex justify-between py-3 text-sm"
          >
            <div>
              <p class="font-medium text-ink">{{ item.product_name }}</p>
              <p class="text-ink-muted">{{ formatPrice(item.price) }} × {{ item.quantity }}</p>
            </div>
            <p class="font-semibold text-ink">{{ formatPrice(item.subtotal) }}</p>
          </div>
        </div>

        <!-- Total -->
        <div class="mt-4 flex justify-between border-t border-surface pt-4 font-bold text-ink">
          <span>Total Pembayaran</span>
          <span class="text-xl">{{ formatPrice(cart.total) }}</span>
        </div>

        <!-- CTA -->
        <button
          :disabled="isPending"
          class="mt-6 w-full rounded-lg bg-accent py-3 font-semibold text-white
                 transition-opacity duration-160 disabled:opacity-50 enabled:hover:opacity-90"
          @click="confirmOrder"
        >
          {{ isPending ? 'Memproses...' : 'Konfirmasi Pesanan' }}
        </button>
      </div>
    </main>
  </div>
</template>

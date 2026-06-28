<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import PublicHeader from '@/components/PublicHeader.vue'
import { useCart }           from '@/features/cart/api/use-cart'
import { useUpdateCartItem } from '@/features/cart/api/use-update-cart-item'
import { useRemoveCartItem } from '@/features/cart/api/use-remove-cart-item'
import { useClearCart }      from '@/features/cart/api/use-clear-cart'
import type { CartItem } from '@/features/cart/api/cart.schema'

const router = useRouter()
const { data: cart, isLoading } = useCart()
const { mutate: updateItem }    = useUpdateCartItem()
const { mutate: removeItem, isPending: removing } = useRemoveCartItem()
const { mutate: clearCart,  isPending: clearing }  = useClearCart()

function formatPrice(n: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
  }).format(n)
}

function onQuantityChange(item: CartItem, qty: number) {
  if (qty < 1) return
  updateItem({ id: item.id, quantity: qty })
}

function onRemove(item: CartItem) {
  removeItem(item.id)
}

function onClear() {
  if (confirm('Hapus semua item dari keranjang?')) clearCart()
}
</script>

<template>
  <div class="min-h-screen bg-canvas">
    <PublicHeader />

    <main class="mx-auto max-w-4xl px-6 py-8">
      <h1 class="text-2xl font-bold text-ink" style="font-family: var(--font-display)">
        Keranjang Belanja
      </h1>

      <!-- Loading -->
      <div v-if="isLoading" class="mt-6 space-y-3">
        <div v-for="n in 3" :key="n" class="h-20 animate-pulse rounded-lg bg-surface" />
      </div>

      <!-- Empty -->
      <div
        v-else-if="!cart?.items.length"
        class="mt-12 rounded-xl border border-surface bg-white px-6 py-16 text-center"
      >
        <svg class="mx-auto h-12 w-12 text-surface" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
               1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
          />
        </svg>
        <p class="mt-4 text-ink-muted">Keranjang kamu masih kosong.</p>
        <button
          class="mt-4 rounded-lg bg-accent px-5 py-2 text-sm font-semibold text-white
                 transition-opacity duration-160 hover:opacity-90"
          @click="router.push({ name: 'products' })"
        >
          Belanja Sekarang
        </button>
      </div>

      <!-- Items -->
      <div v-else class="mt-6 grid gap-6 lg:grid-cols-3">

        <!-- Item list (2/3 width) -->
        <div class="lg:col-span-2 space-y-3">
          <div
            v-for="item in cart.items"
            :key="item.id"
            class="flex gap-4 rounded-xl border border-surface bg-white p-4"
          >
            <!-- Image -->
            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-canvas">
              <img
                v-if="item.product_image_url"
                :src="item.product_image_url"
                :alt="item.product_name"
                class="h-full w-full object-cover"
              />
            </div>

            <!-- Info -->
            <div class="flex flex-1 flex-col gap-1">
              <p class="font-semibold text-ink leading-snug">{{ item.product_name }}</p>
              <p class="text-sm text-ink-muted">{{ formatPrice(item.price) }} / pcs</p>
              <div class="flex items-center gap-3 mt-1">
                <!-- Qty control -->
                <div class="flex items-center gap-1">
                  <button
                    class="flex h-6 w-6 items-center justify-center rounded border border-surface
                           text-ink-muted transition-colors hover:border-ink hover:text-ink"
                    :disabled="item.quantity <= 1"
                    @click="onQuantityChange(item, item.quantity - 1)"
                  >−</button>
                  <span class="w-8 text-center text-sm font-semibold text-ink">{{ item.quantity }}</span>
                  <button
                    class="flex h-6 w-6 items-center justify-center rounded border border-surface
                           text-ink-muted transition-colors hover:border-ink hover:text-ink"
                    @click="onQuantityChange(item, item.quantity + 1)"
                  >+</button>
                </div>
                <button
                  class="text-xs text-ink-muted transition-colors hover:text-accent"
                  :disabled="removing"
                  @click="onRemove(item)"
                >
                  Hapus
                </button>
              </div>
            </div>

            <!-- Subtotal -->
            <div class="shrink-0 text-right">
              <p class="font-bold text-ink">{{ formatPrice(item.subtotal) }}</p>
            </div>
          </div>

          <!-- Clear cart -->
          <div class="flex justify-end">
            <button
              class="text-xs text-ink-muted transition-colors hover:text-accent"
              :disabled="clearing"
              @click="onClear"
            >
              Hapus semua
            </button>
          </div>
        </div>

        <!-- Summary (1/3 width) -->
        <div class="rounded-xl border border-surface bg-white p-5 self-start">
          <h2 class="font-bold text-ink" style="font-family: var(--font-display)">
            Ringkasan
          </h2>
          <div class="mt-4 space-y-2 text-sm">
            <div
              v-for="item in cart.items"
              :key="item.id"
              class="flex justify-between text-ink-muted"
            >
              <span class="truncate pr-2">{{ item.product_name }} ×{{ item.quantity }}</span>
              <span class="shrink-0">{{ formatPrice(item.subtotal) }}</span>
            </div>
          </div>
          <div class="mt-4 border-t border-surface pt-4 flex justify-between font-bold text-ink">
            <span>Total</span>
            <span>{{ formatPrice(cart.total) }}</span>
          </div>
          <button
            class="mt-5 w-full rounded-lg bg-accent py-2.5 text-sm font-semibold text-white
                   transition-opacity duration-160 hover:opacity-90"
            @click="router.push({ name: 'checkout' })"
          >
            Lanjut ke Checkout →
          </button>
          <button
            class="mt-2 w-full rounded-lg border border-surface py-2 text-sm text-ink-muted
                   transition-colors duration-160 hover:border-ink"
            @click="router.push({ name: 'products' })"
          >
            Lanjutkan Belanja
          </button>
        </div>

      </div>
    </main>
  </div>
</template>

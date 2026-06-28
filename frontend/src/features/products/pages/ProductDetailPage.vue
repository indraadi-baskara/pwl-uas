<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PublicHeader from '@/components/PublicHeader.vue'
import { useAddToCart }  from '@/features/cart/api/use-add-to-cart'
import { useProduct }    from '@/features/products/api/use-product'
import { useAuthStore }  from '@/stores/auth'

const route  = useRoute()
const router = useRouter()
const auth   = useAuthStore()
const id     = computed(() => Number(route.params.id))

const { data: product, isLoading, isError } = useProduct(id)
const { mutate: addToCart, isPending: adding } = useAddToCart()

const qty    = ref(1)
const added  = ref(false)

function formatPrice(price: number) {
  return new Intl.NumberFormat('id-ID', {
    style:                 'currency',
    currency:              'IDR',
    maximumFractionDigits: 0,
  }).format(price)
}

function onAddToCart() {
  if (!product.value) return

  if (!auth.isAuthenticated) {
    void router.push({ name: 'login' })
    return
  }

  addToCart({ product_id: product.value.id, quantity: qty.value }, {
    onSuccess: () => {
      added.value = true
      setTimeout(() => { added.value = false }, 2000)
    },
  })
}
</script>

<template>
  <div class="min-h-screen bg-canvas">
    <PublicHeader />

    <main class="mx-auto max-w-4xl px-6 py-8">

      <!-- Loading skeleton -->
      <div v-if="isLoading" class="animate-pulse grid gap-8 md:grid-cols-2">
        <div class="aspect-square rounded-xl bg-surface" />
        <div class="space-y-4">
          <div class="h-4 w-20 rounded bg-surface" />
          <div class="h-8 w-full rounded bg-surface" />
          <div class="h-6 w-32 rounded bg-surface" />
          <div class="h-4 w-24 rounded bg-surface" />
        </div>
      </div>

      <!-- Error / Not found -->
      <div v-else-if="isError" class="rounded-xl bg-accent-soft px-6 py-12 text-center">
        <p class="text-accent">Produk tidak ditemukan.</p>
        <button
          class="mt-4 text-sm underline text-accent"
          @click="router.push({ name: 'products' })"
        >
          Kembali ke katalog
        </button>
      </div>

      <!-- Detail -->
      <div v-else-if="product" class="grid gap-8 md:grid-cols-2">
        <!-- Image -->
        <div class="aspect-square overflow-hidden rounded-xl border border-surface bg-white">
          <img
            v-if="product.image_url"
            :src="product.image_url"
            :alt="product.name"
            class="h-full w-full object-cover"
          />
          <div v-else class="flex h-full items-center justify-center">
            <svg class="h-20 w-20 text-surface" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828
                   0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
          </div>
        </div>

        <!-- Info -->
        <div>
          <p v-if="product.category" class="text-xs font-semibold uppercase tracking-wider text-accent">
            {{ product.category }}
          </p>
          <h1
            class="mt-1 text-2xl font-bold text-ink"
            style="font-family: var(--font-display)"
          >
            {{ product.name }}
          </h1>
          <p class="mt-3 text-3xl font-bold text-ink">
            {{ formatPrice(product.price) }}
          </p>

          <div class="mt-4 flex items-center gap-2">
            <span
              :class="[
                'rounded px-2 py-0.5 text-xs font-semibold',
                product.stock > 0 ? 'bg-green-50 text-green-700' : 'bg-accent-soft text-accent',
              ]"
            >
              {{ product.stock > 0 ? `Stok: ${product.stock}` : 'Habis' }}
            </span>
          </div>

          <p v-if="product.description" class="mt-4 text-sm leading-relaxed text-ink-muted">
            {{ product.description }}
          </p>

          <!-- Add to cart (buyer only) -->
          <div v-if="product.stock > 0 && !auth.isAdmin" class="mt-6 flex items-center gap-3">
            <!-- Qty selector -->
            <div class="flex items-center rounded-lg border border-surface">
              <button
                class="px-3 py-2 text-ink-muted transition-colors hover:text-ink disabled:opacity-40"
                :disabled="qty <= 1"
                @click="qty--"
              >−</button>
              <span class="w-10 text-center font-semibold text-ink">{{ qty }}</span>
              <button
                class="px-3 py-2 text-ink-muted transition-colors hover:text-ink"
                :disabled="qty >= product.stock"
                @click="qty++"
              >+</button>
            </div>

            <button
              :disabled="adding"
              :class="[
                'flex-1 rounded-lg py-2.5 font-semibold text-white transition-all duration-160',
                added
                  ? 'bg-green-600'
                  : 'bg-accent enabled:hover:opacity-90 disabled:opacity-50',
              ]"
              @click="onAddToCart"
            >
              {{ added ? '✓ Ditambahkan!' : adding ? 'Menambahkan...' : 'Tambah ke Keranjang' }}
            </button>
          </div>
        </div>
      </div>

    </main>
  </div>
</template>

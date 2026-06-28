<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProduct } from '@/features/products/api/use-product'

const route  = useRoute()
const router = useRouter()
const id     = computed(() => Number(route.params.id))

const { data: product, isLoading, isError } = useProduct(id)

function formatPrice(price: number) {
  return new Intl.NumberFormat('id-ID', {
    style:                 'currency',
    currency:              'IDR',
    maximumFractionDigits: 0,
  }).format(price)
}
</script>

<template>
  <div class="min-h-screen bg-canvas">

    <!-- Header -->
    <header class="border-b border-surface bg-white">
      <div class="mx-auto max-w-4xl px-6 py-5">
        <button
          class="flex items-center gap-1 text-sm text-ink-muted transition-colors duration-160 hover:text-ink"
          @click="router.back()"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali
        </button>
      </div>
    </header>

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
                product.stock > 0
                  ? 'bg-green-50 text-green-700'
                  : 'bg-accent-soft text-accent',
              ]"
            >
              {{ product.stock > 0 ? `Stok: ${product.stock}` : 'Habis' }}
            </span>
          </div>

          <p v-if="product.description" class="mt-4 text-sm leading-relaxed text-ink-muted">
            {{ product.description }}
          </p>
        </div>
      </div>

    </main>
  </div>
</template>

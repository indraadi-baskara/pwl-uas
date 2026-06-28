<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProducts } from '@/features/products/api/use-products'
import ProductCard from '@/features/products/components/ProductCard.vue'
import Pagination from '@/features/products/components/Pagination.vue'
import type { Product } from '@/features/products/api/product.schema'

const route  = useRoute()
const router = useRouter()

const search   = ref(String(route.query.search   ?? ''))
const category = ref(String(route.query.category ?? ''))
const page     = ref(Number(route.query.page     ?? 1))

const filters = computed(() => ({
  page:     page.value,
  limit:    12,
  search:   search.value || undefined,
  category: category.value || undefined,
}))

const { data, isLoading, isError } = useProducts(filters)

function syncUrl() {
  void router.replace({
    query: {
      ...(search.value   ? { search:   search.value }   : {}),
      ...(category.value ? { category: category.value } : {}),
      ...(page.value > 1 ? { page:     String(page.value) } : {}),
    },
  })
}

function onSearch() {
  page.value = 1
  syncUrl()
}

function onPageChange(p: number) {
  page.value = p
  syncUrl()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

watch(() => route.query, (q) => {
  search.value   = String(q.search   ?? '')
  category.value = String(q.category ?? '')
  page.value     = Number(q.page     ?? 1)
})

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
      <div class="mx-auto max-w-6xl px-6 py-5">
        <h1
          class="text-2xl font-bold text-ink"
          style="font-family: var(--font-display)"
        >
          Depo Waroeng Ban
        </h1>
        <p class="mt-0.5 text-sm text-ink-muted">Katalog Ban Lengkap</p>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-8">

      <!-- Filters -->
      <div class="mb-6 flex flex-col gap-3 sm:flex-row">
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-muted"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
          </svg>
          <input
            v-model="search"
            type="text"
            placeholder="Cari produk..."
            class="w-full rounded-lg border border-surface bg-white py-2 pl-9 pr-3 text-sm
                   text-ink placeholder-ink-muted outline-none transition-colors
                   duration-160 focus:border-ink"
            @keydown.enter="onSearch"
          />
        </div>
        <input
          v-model="category"
          type="text"
          placeholder="Filter kategori"
          class="rounded-lg border border-surface bg-white px-3 py-2 text-sm text-ink
                 placeholder-ink-muted outline-none transition-colors duration-160
                 focus:border-ink sm:w-44"
          @keydown.enter="onSearch"
        />
        <button
          class="rounded-lg bg-accent px-5 py-2 text-sm font-semibold text-white
                 transition-opacity duration-160 hover:opacity-90"
          @click="onSearch"
        >
          Cari
        </button>
      </div>

      <!-- Loading skeleton -->
      <div v-if="isLoading" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        <div
          v-for="n in 8"
          :key="n"
          class="animate-pulse rounded-lg border border-surface bg-white"
        >
          <div class="aspect-square bg-surface" />
          <div class="p-4 space-y-2">
            <div class="h-3 w-16 rounded bg-surface" />
            <div class="h-4 w-full rounded bg-surface" />
            <div class="h-4 w-3/4 rounded bg-surface" />
          </div>
        </div>
      </div>

      <!-- Error -->
      <div v-else-if="isError" class="rounded-lg border border-accent-soft bg-accent-soft px-6 py-8 text-center">
        <p class="text-sm text-accent">Gagal memuat produk. Coba lagi.</p>
      </div>

      <!-- Empty -->
      <div
        v-else-if="!data?.data.length"
        class="rounded-lg border border-surface bg-white px-6 py-16 text-center"
      >
        <p class="text-ink-muted">Tidak ada produk ditemukan.</p>
      </div>

      <!-- Grid -->
      <div v-else>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
          <ProductCard
            v-for="product in data.data"
            :key="product.id"
            :product="product"
          />
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex flex-col items-center gap-2">
          <Pagination :pagination="data.pagination" @change="onPageChange" />
          <p class="text-xs text-ink-muted">
            {{ data.pagination.total }} produk ditemukan
          </p>
        </div>
      </div>

    </main>
  </div>
</template>

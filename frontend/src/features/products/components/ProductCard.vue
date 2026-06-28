<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { Product } from '@/features/products/api/product.schema'

const props = defineProps<{
  product: Product
  admin?:  boolean
}>()

const emit = defineEmits<{
  edit:   [product: Product]
  delete: [product: Product]
}>()

function formatPrice(price: number) {
  return new Intl.NumberFormat('id-ID', {
    style:                 'currency',
    currency:              'IDR',
    maximumFractionDigits: 0,
  }).format(price)
}
</script>

<template>
  <component
    :is="admin ? 'div' : RouterLink"
    :to="admin ? undefined : { name: 'product-detail', params: { id: product.id } }"
    class="group block overflow-hidden rounded-lg border border-surface bg-white transition-shadow duration-160 hover:shadow-md"
  >
    <!-- Image -->
    <div class="aspect-square w-full overflow-hidden bg-canvas">
      <img
        v-if="product.image_url"
        :src="product.image_url"
        :alt="product.name"
        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
      />
      <div v-else class="flex h-full items-center justify-center">
        <svg class="h-12 w-12 text-surface" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828
               0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
      </div>
    </div>

    <!-- Info -->
    <div class="p-4">
      <p v-if="product.category" class="text-xs font-semibold uppercase tracking-wider text-accent">
        {{ product.category }}
      </p>
      <h3
        class="mt-1 line-clamp-2 font-semibold text-ink"
        style="font-family: var(--font-display)"
      >
        {{ product.name }}
      </h3>
      <p class="mt-1 text-sm font-bold text-ink">
        {{ formatPrice(product.price) }}
      </p>
      <p class="mt-0.5 text-xs text-ink-muted">
        Stok: {{ product.stock }}
      </p>

      <!-- Admin actions -->
      <div v-if="admin" class="mt-3 flex gap-2">
        <button
          class="flex-1 rounded border border-surface px-3 py-1.5 text-xs text-ink-muted
                 transition-colors duration-160 hover:border-ink hover:text-ink"
          @click="emit('edit', product)"
        >
          Edit
        </button>
        <button
          class="flex-1 rounded border border-surface px-3 py-1.5 text-xs text-ink-muted
                 transition-colors duration-160 hover:border-accent hover:text-accent"
          @click="emit('delete', product)"
        >
          Hapus
        </button>
      </div>
    </div>
  </component>
</template>

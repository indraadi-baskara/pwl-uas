<script setup lang="ts">
import { computed } from 'vue'
import type { Pagination } from '@/features/products/api/get-products'

const props = defineProps<{ pagination: Pagination }>()
const emit  = defineEmits<{ change: [page: number] }>()

const pages = computed(() => {
  const total = props.pagination.last_page
  const cur   = props.pagination.current_page
  const delta = 2
  const range: number[] = []

  for (let i = Math.max(1, cur - delta); i <= Math.min(total, cur + delta); i++) {
    range.push(i)
  }
  return range
})
</script>

<template>
  <div v-if="pagination.last_page > 1" class="flex items-center justify-center gap-1">
    <!-- Prev -->
    <button
      :disabled="pagination.current_page <= 1"
      class="flex h-8 w-8 items-center justify-center rounded border border-surface text-sm
             text-ink-muted transition-colors duration-160 disabled:opacity-40
             enabled:hover:border-accent enabled:hover:text-accent"
      @click="emit('change', pagination.current_page - 1)"
    >
      ‹
    </button>

    <!-- Leading ellipsis -->
    <span v-if="pages[0] > 1" class="px-1 text-xs text-ink-muted">…</span>

    <!-- Page numbers -->
    <button
      v-for="p in pages"
      :key="p"
      :class="[
        'flex h-8 w-8 items-center justify-center rounded border text-sm transition-colors duration-160',
        p === pagination.current_page
          ? 'border-accent bg-accent font-semibold text-white'
          : 'border-surface text-ink-muted hover:border-accent hover:text-accent',
      ]"
      @click="emit('change', p)"
    >
      {{ p }}
    </button>

    <!-- Trailing ellipsis -->
    <span v-if="pages[pages.length - 1] < pagination.last_page" class="px-1 text-xs text-ink-muted">…</span>

    <!-- Next -->
    <button
      :disabled="pagination.current_page >= pagination.last_page"
      class="flex h-8 w-8 items-center justify-center rounded border border-surface text-sm
             text-ink-muted transition-colors duration-160 disabled:opacity-40
             enabled:hover:border-accent enabled:hover:text-accent"
      @click="emit('change', pagination.current_page + 1)"
    >
      ›
    </button>
  </div>
</template>

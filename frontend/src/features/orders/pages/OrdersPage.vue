<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import PublicHeader from '@/components/PublicHeader.vue'
import Pagination from '@/features/products/components/Pagination.vue'
import { useOrders } from '@/features/orders/api/use-orders'
import { STATUS_LABELS, STATUS_COLORS } from '@/features/orders/api/order.schema'
import type { Order } from '@/features/orders/api/order.schema'

const router = useRouter()
const page   = ref(1)

const { data, isLoading } = useOrders(page)

function formatPrice(n: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
  }).format(n)
}

function formatDate(iso: string) {
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(iso))
}

function goToDetail(order: Order) {
  void router.push({ name: 'order-detail', params: { id: order.id } })
}

function onPageChange(p: number) {
  page.value = p
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<template>
  <div class="min-h-screen bg-canvas">
    <PublicHeader />

    <main class="mx-auto max-w-4xl px-6 py-8">
      <h1 class="text-2xl font-bold text-ink" style="font-family: var(--font-display)">
        Riwayat Pesanan
      </h1>

      <!-- Loading -->
      <div v-if="isLoading" class="mt-6 space-y-3">
        <div v-for="n in 5" :key="n" class="h-16 animate-pulse rounded-lg bg-surface" />
      </div>

      <!-- Empty -->
      <div
        v-else-if="!data?.data.length"
        class="mt-8 rounded-xl border border-surface bg-white px-6 py-16 text-center"
      >
        <p class="text-ink-muted">Belum ada pesanan.</p>
        <button
          class="mt-4 rounded-lg bg-accent px-5 py-2 text-sm font-semibold text-white
                 transition-opacity hover:opacity-90"
          @click="router.push({ name: 'products' })"
        >
          Mulai Belanja
        </button>
      </div>

      <!-- Table -->
      <div v-else class="mt-6">
        <div class="overflow-hidden rounded-xl border border-surface bg-white">
          <table class="w-full text-sm">
            <thead class="border-b border-surface bg-canvas">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-ink-muted">ID</th>
                <th class="px-4 py-3 text-left font-semibold text-ink-muted">Tanggal</th>
                <th class="px-4 py-3 text-left font-semibold text-ink-muted">Status</th>
                <th class="px-4 py-3 text-right font-semibold text-ink-muted">Total</th>
                <th class="px-4 py-3" />
              </tr>
            </thead>
            <tbody class="divide-y divide-surface">
              <tr
                v-for="order in data.data"
                :key="order.id"
                class="hover:bg-canvas cursor-pointer"
                @click="goToDetail(order)"
              >
                <td class="px-4 py-3 font-mono text-ink-muted">#{{ order.id }}</td>
                <td class="px-4 py-3 text-ink-muted">{{ formatDate(order.created_at) }}</td>
                <td class="px-4 py-3">
                  <span
                    :class="[
                      'rounded px-2 py-0.5 text-xs font-semibold',
                      STATUS_COLORS[order.status],
                    ]"
                  >
                    {{ STATUS_LABELS[order.status] }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right font-semibold text-ink">
                  {{ formatPrice(order.total) }}
                </td>
                <td class="px-4 py-3 text-right text-ink-muted">›</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-6 flex justify-center">
          <Pagination :pagination="data.pagination" @change="onPageChange" />
        </div>
      </div>
    </main>
  </div>
</template>

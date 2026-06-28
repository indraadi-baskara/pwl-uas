<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AdminHeader from '@/components/AdminHeader.vue'
import { type Order, STATUS_COLORS, STATUS_LABELS } from '@/features/orders/api/order.schema'
import { useOrders } from '@/features/orders/api/use-orders'
import Pagination from '@/features/products/components/Pagination.vue'

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
  void router.push({ name: 'admin-order-detail', params: { id: order.id } })
}

function onPageChange(p: number) {
  page.value = p
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<template>
  <div class="min-h-screen bg-canvas">

    <AdminHeader back="Dasbor" back-route="admin" title="Kelola Pesanan" />

    <main class="mx-auto max-w-5xl px-6 py-8">

      <!-- Loading -->
      <div v-if="isLoading" class="space-y-3">
        <div v-for="n in 5" :key="n" class="h-14 animate-pulse rounded-lg bg-surface" />
      </div>

      <!-- Empty -->
      <div
        v-else-if="!data?.data.length"
        class="rounded-xl border border-surface bg-white px-6 py-16 text-center"
      >
        <p class="text-ink-muted">Belum ada pesanan masuk.</p>
      </div>

      <!-- Table -->
      <div v-else>
        <div class="overflow-hidden rounded-xl border border-surface bg-white">
          <table class="w-full text-sm">
            <thead class="border-b border-surface bg-canvas">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-ink-muted">ID</th>
                <th class="px-4 py-3 text-left font-semibold text-ink-muted">ID Pelanggan</th>
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
                class="cursor-pointer hover:bg-canvas"
                @click="goToDetail(order)"
              >
                <td class="px-4 py-3 font-mono text-ink-muted">#{{ order.id }}</td>
                <td class="px-4 py-3 text-ink">{{ order.user_id }}</td>
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

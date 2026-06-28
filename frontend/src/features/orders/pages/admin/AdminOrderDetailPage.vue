<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AdminHeader from '@/components/AdminHeader.vue'
import { type OrderStatus, STATUS_COLORS, STATUS_LABELS } from '@/features/orders/api/order.schema'
import { useOrder } from '@/features/orders/api/use-order'
import { useUpdateOrderStatus } from '@/features/orders/api/use-update-order-status'

const route = useRoute()
const id    = computed(() => Number(route.params.id))

const { data: order, isLoading, isError } = useOrder(id)
const { mutate: changeStatus, isPending: updatingStatus } = useUpdateOrderStatus(id.value)

const STATUSES: OrderStatus[] = ['pending', 'processing', 'shipped', 'completed', 'cancelled']

function formatPrice(n: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
  }).format(n)
}

function formatDate(iso: string) {
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'long', timeStyle: 'short' }).format(new Date(iso))
}
</script>

<template>
  <div class="min-h-screen bg-canvas">

    <AdminHeader back="Semua Pesanan" back-route="admin-orders" title="Detail Pesanan" />

    <main class="mx-auto max-w-3xl px-6 py-8">

      <!-- Loading -->
      <div v-if="isLoading" class="animate-pulse space-y-4">
        <div class="h-8 w-48 rounded bg-surface" />
        <div class="h-24 rounded-xl bg-surface" />
        <div class="h-48 rounded-xl bg-surface" />
      </div>

      <!-- Error -->
      <div v-else-if="isError" class="rounded-xl bg-accent-soft px-6 py-12 text-center">
        <p class="text-accent">Pesanan tidak ditemukan.</p>
      </div>

      <div v-else-if="order">
        <!-- Title + status badge -->
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-2xl font-bold text-ink" style="font-family: var(--font-display)">
              Pesanan #{{ order.id }}
            </h2>
            <p class="mt-1 text-sm text-ink-muted">
              ID Pelanggan: {{ order.user_id }} &middot; {{ formatDate(order.created_at) }}
            </p>
          </div>
          <span
            :class="['rounded px-3 py-1 text-sm font-semibold', STATUS_COLORS[order.status]]"
          >
            {{ STATUS_LABELS[order.status] }}
          </span>
        </div>

        <!-- Status update -->
        <div class="mt-5 rounded-xl border border-surface bg-white p-4">
          <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-ink-muted">
            Update Status
          </p>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="s in STATUSES"
              :key="s"
              :disabled="order.status === s || updatingStatus"
              :class="[
                'rounded px-3 py-1.5 text-xs font-semibold transition-colors duration-160',
                order.status === s
                  ? 'cursor-default bg-ink text-white'
                  : 'border border-surface text-ink-muted hover:border-ink hover:text-ink disabled:opacity-40',
              ]"
              @click="changeStatus(s)"
            >
              {{ STATUS_LABELS[s] }}
            </button>
          </div>
        </div>

        <!-- Order items -->
        <div class="mt-6 overflow-hidden rounded-xl border border-surface bg-white">
          <table class="w-full text-sm">
            <thead class="border-b border-surface bg-canvas">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-ink-muted">Produk</th>
                <th class="px-4 py-3 text-center font-semibold text-ink-muted">Qty</th>
                <th class="px-4 py-3 text-right font-semibold text-ink-muted">Harga</th>
                <th class="px-4 py-3 text-right font-semibold text-ink-muted">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface">
              <tr v-for="item in order.items" :key="item.id">
                <td class="px-4 py-3 text-ink">{{ item.name }}</td>
                <td class="px-4 py-3 text-center text-ink-muted">{{ item.quantity }}</td>
                <td class="px-4 py-3 text-right text-ink-muted">{{ formatPrice(item.price) }}</td>
                <td class="px-4 py-3 text-right font-semibold text-ink">{{ formatPrice(item.subtotal) }}</td>
              </tr>
            </tbody>
          </table>
          <div class="flex justify-between border-t border-surface px-4 py-3 font-bold text-ink">
            <span>Total</span>
            <span>{{ formatPrice(order.total) }}</span>
          </div>
        </div>
      </div>

    </main>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import PublicHeader from "@/components/PublicHeader.vue";
import {
  STATUS_COLORS,
  STATUS_LABELS,
} from "@/features/orders/api/order.schema";
import { useOrder } from "@/features/orders/api/use-order";

const route = useRoute();
const router = useRouter();
const id = computed(() => Number(route.params.id));

const { data: order, isLoading, isError } = useOrder(id);

function formatPrice(n: number) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(n);
}

function formatDate(iso: string) {
  if (!iso) return;

  let validIsoString = iso;

  if (iso?.includes(" ")) {
    console.warn(
      "The provided ISO string contains a space instead of 'T'. This may lead to incorrect date parsing.",
      iso,
    );
    validIsoString = iso?.replace(" ", "T")?.replace("+00", "Z");
  }

  return new Intl.DateTimeFormat("id-ID", {
    dateStyle: "long",
    timeStyle: "short",
  }).format(new Date(validIsoString));
}
</script>

<template>
  <div class="min-h-screen bg-canvas">
    <PublicHeader />

    <main class="mx-auto max-w-3xl px-6 py-8">
      <button
        class="mb-4 flex items-center gap-1 text-sm text-ink-muted transition-colors hover:text-ink"
        @click="router.push({ name: 'orders' })"
      >
        ← Riwayat Pesanan
      </button>

      <!-- Loading -->
      <div v-if="isLoading" class="animate-pulse space-y-4">
        <div class="h-8 w-48 rounded bg-surface" />
        <div class="h-32 rounded-xl bg-surface" />
        <div class="h-48 rounded-xl bg-surface" />
      </div>

      <!-- Error / Not found -->
      <div
        v-else-if="isError"
        class="rounded-xl bg-accent-soft px-6 py-12 text-center"
      >
        <p class="text-accent">Pesanan tidak ditemukan.</p>
      </div>

      <div v-else-if="order">
        <!-- Header -->
        <div class="flex items-start justify-between">
          <div>
            <h1
              class="text-2xl font-bold text-ink"
              style="font-family: var(--font-display)"
            >
              Pesanan #{{ order.id }}
            </h1>
            <p class="mt-1 text-sm text-ink-muted">
              {{ formatDate(order.created_at) }}
            </p>
          </div>
          <span
            :class="[
              'rounded px-3 py-1 text-sm font-semibold',
              STATUS_COLORS[order.status],
            ]"
          >
            {{ STATUS_LABELS[order.status] }}
          </span>
        </div>

        <!-- Items -->
        <div
          class="mt-6 overflow-hidden rounded-xl border border-surface bg-white"
        >
          <table class="w-full text-sm">
            <thead class="border-b border-surface bg-canvas">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-ink-muted">
                  Produk
                </th>
                <th class="px-4 py-3 text-center font-semibold text-ink-muted">
                  Qty
                </th>
                <th class="px-4 py-3 text-right font-semibold text-ink-muted">
                  Harga
                </th>
                <th class="px-4 py-3 text-right font-semibold text-ink-muted">
                  Subtotal
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface">
              <tr v-for="item in order.items" :key="item.id">
                <td class="px-4 py-3 text-ink">{{ item.name }}</td>
                <td class="px-4 py-3 text-center text-ink-muted">
                  {{ item.quantity }}
                </td>
                <td class="px-4 py-3 text-right text-ink-muted">
                  {{ formatPrice(item.price) }}
                </td>
                <td class="px-4 py-3 text-right font-semibold text-ink">
                  {{ formatPrice(item.subtotal) }}
                </td>
              </tr>
            </tbody>
          </table>
          <div
            class="flex justify-between border-t border-surface px-4 py-3 font-bold text-ink"
          >
            <span>Total</span>
            <span>{{ formatPrice(order.total) }}</span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

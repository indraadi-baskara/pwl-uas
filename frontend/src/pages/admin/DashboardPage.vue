<script setup lang="ts">
import { useRouter } from 'vue-router'
import AdminHeader from '@/components/AdminHeader.vue'

const router = useRouter()
</script>

<template>
  <div class="min-h-screen bg-canvas">

    <AdminHeader />

    <main class="mx-auto max-w-5xl px-6 py-12">
      <p class="text-xs font-semibold uppercase tracking-widest text-accent">Selamat datang</p>
      <h2
        class="mt-1 text-3xl font-bold text-ink"
        style="font-family: var(--font-display)"
      >
        Dasbor Admin
      </h2>
      <p class="mt-2 text-ink-muted">Kelola toko ban Anda dari sini.</p>

      <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <button
          class="group flex items-start gap-4 rounded-xl border border-surface bg-white p-5
                 text-left transition-shadow duration-160 hover:shadow-md"
          @click="router.push({ name: 'admin-products' })"
        >
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent-soft">
            <svg class="h-5 w-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <div>
            <p class="font-semibold text-ink" style="font-family: var(--font-display)">Produk</p>
            <p class="mt-0.5 text-xs text-ink-muted">Tambah, edit, dan hapus produk ban</p>
          </div>
        </button>

        <button
          class="group flex items-start gap-4 rounded-xl border border-surface bg-white p-5
                 text-left transition-shadow duration-160 hover:shadow-md"
          @click="router.push({ name: 'admin-orders' })"
        >
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent-soft">
            <svg class="h-5 w-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9
                   5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <div>
            <p class="font-semibold text-ink" style="font-family: var(--font-display)">Pesanan</p>
            <p class="mt-0.5 text-xs text-ink-muted">Lihat dan update status semua pesanan</p>
          </div>
        </button>
      </div>
    </main>

  </div>
</template>

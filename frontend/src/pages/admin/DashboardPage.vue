<script setup lang="ts">
import { useRouter } from 'vue-router'
import { postLogout } from '@/features/auth'
import { useAuthStore } from '@/stores/auth'

const auth   = useAuthStore()
const router = useRouter()

async function logout() {
  await postLogout()
  auth.clearUser()
  void router.push({ name: 'login' })
}
</script>

<template>
  <div class="min-h-screen bg-[#F7F6F4]">

    <!-- Top nav -->
    <header class="border-b border-[#EDEDEB] bg-white shadow-sm">
      <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
        <h1
          class="font-bold text-[#1C1C1E]"
          style="font-family: var(--font-display)"
        >
          Depo Waroeng Ban
          <span class="ml-2 rounded-sm bg-[#F9EBEA] px-2 py-0.5 text-xs font-semibold text-[#D9251D]">
            Admin
          </span>
        </h1>
        <div class="flex items-center gap-4">
          <span class="text-sm text-[#6B6B70]">{{ auth.user?.email }}</span>
          <button
            class="rounded-sm border border-[#EDEDEB] px-3 py-1.5 text-sm text-[#6B6B70]
                   transition-colors duration-[160ms] hover:border-[#D9251D] hover:text-[#D9251D]"
            @click="logout"
          >
            Keluar
          </button>
        </div>
      </div>
    </header>

    <!-- Body -->
    <main class="mx-auto max-w-5xl px-6 py-12">
      <p class="text-xs font-semibold uppercase tracking-widest text-[#D9251D]">Selamat datang</p>
      <h2
        class="mt-1 text-3xl font-bold text-[#1C1C1E]"
        style="font-family: var(--font-display)"
      >
        Dasbor Admin
      </h2>
      <p class="mt-2 text-[#6B6B70]">Kelola toko ban Anda dari sini.</p>

      <!-- Quick links -->
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
            <p class="font-semibold text-ink" style="font-family: var(--font-display)">
              Produk
            </p>
            <p class="mt-0.5 text-xs text-ink-muted">Tambah, edit, dan hapus produk ban</p>
          </div>
        </button>
      </div>
    </main>

  </div>
</template>

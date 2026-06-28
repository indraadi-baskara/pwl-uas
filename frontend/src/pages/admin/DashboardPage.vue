<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { postLogout } from '@/features/auth'
import { useRouter } from 'vue-router'

const auth   = useAuthStore()
const router = useRouter()

async function logout() {
  await postLogout()
  auth.clearUser()
  router.push({ name: 'login' })
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
      <p class="mt-2 text-[#6B6B70]">
        Fitur produk, keranjang, dan pesanan akan tersedia di sini.
      </p>
    </main>

  </div>
</template>

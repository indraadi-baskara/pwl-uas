<script setup lang="ts">
import { useRouter } from 'vue-router'
import { postLogout } from '@/features/auth'
import { useAuthStore } from '@/stores/auth'

withDefaults(defineProps<{
  title?:     string
  back?:      string
  backRoute?: string
}>(), {
  title:     '',
  back:      '',
  backRoute: 'admin',
})

const auth   = useAuthStore()
const router = useRouter()

async function logout() {
  await postLogout()
  auth.clearUser()
  void router.push({ name: 'login' })
}
</script>

<template>
  <header class="border-b border-surface bg-white shadow-sm">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">

      <!-- Left: back + title, or brand when at root -->
      <div class="flex items-center gap-3">
        <template v-if="back">
          <button
            class="text-sm text-ink-muted transition-colors duration-160 hover:text-ink"
            @click="router.push({ name: backRoute })"
          >
            ← {{ back }}
          </button>
          <h1
            v-if="title"
            class="font-bold text-ink"
            style="font-family: var(--font-display)"
          >
            {{ title }}
          </h1>
        </template>
        <template v-else>
          <span class="font-bold text-ink" style="font-family: var(--font-display)">
            Depo Waroeng Ban
          </span>
          <span class="rounded-sm bg-accent-soft px-2 py-0.5 text-xs font-semibold text-accent">
            Admin
          </span>
        </template>
      </div>

      <!-- Right: optional action + user email + logout -->
      <div class="flex items-center gap-3">
        <slot name="action" />
        <span class="text-sm text-ink-muted">{{ auth.user?.email }}</span>
        <button
          class="rounded border border-surface px-3 py-1.5 text-sm text-ink-muted
                 transition-colors duration-160 hover:border-accent hover:text-accent"
          @click="logout"
        >
          Keluar
        </button>
      </div>

    </div>
  </header>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useRegister } from '../api/use-register'

const router = useRouter()
const { mutate: register, isPending, isError, error } = useRegister()

const form = reactive({
  email:    '',
  password: '',
  role:     'user' as 'user' | 'admin',
})
const submitted = ref(false)

function handleSubmit() {
  submitted.value = true
  register({ email: form.email, password: form.password, role: form.role })
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-canvas px-4">
    <div class="w-full max-w-sm">

      <!-- Brand header -->
      <div class="mb-8 text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-accent">
          Depo Waroeng Ban
        </p>
        <h1
          class="mt-1 text-3xl font-bold tracking-tight text-ink"
          style="font-family: var(--font-display)"
        >
          Buat Akun
        </h1>
        <p class="mt-1 text-sm text-ink-muted">Daftar sebagai pembeli atau penjual</p>
      </div>

      <!-- Card -->
      <div class="rounded-xl bg-white px-8 py-8 shadow-sm">

        <!-- Error banner -->
        <div
          v-if="isError"
          role="alert"
          class="mb-5 rounded-lg bg-accent-soft px-4 py-3 text-sm text-accent"
        >
          {{ error?.message ?? 'Pendaftaran gagal. Coba lagi.' }}
        </div>

        <form novalidate @submit.prevent="handleSubmit">

          <!-- Role selector -->
          <div class="mb-5">
            <p class="mb-2 text-sm font-medium text-ink">Daftar sebagai</p>
            <div class="grid grid-cols-2 gap-2">
              <button
                type="button"
                :class="[
                  'rounded-lg border px-4 py-3 text-sm font-semibold transition-colors duration-160',
                  form.role === 'user'
                    ? 'border-accent bg-accent-soft text-accent'
                    : 'border-surface text-ink-muted hover:border-accent hover:text-accent',
                ]"
                @click="form.role = 'user'"
              >
                <span class="block text-base">🛒</span>
                Pembeli
              </button>
              <button
                type="button"
                :class="[
                  'rounded-lg border px-4 py-3 text-sm font-semibold transition-colors duration-160',
                  form.role === 'admin'
                    ? 'border-accent bg-accent-soft text-accent'
                    : 'border-surface text-ink-muted hover:border-accent hover:text-accent',
                ]"
                @click="form.role = 'admin'"
              >
                <span class="block text-base">🏪</span>
                Penjual
              </button>
            </div>
          </div>

          <!-- Email -->
          <div class="mb-4">
            <label for="reg-email" class="mb-1 block text-sm font-medium text-ink">
              Email
            </label>
            <input
              id="reg-email"
              v-model="form.email"
              type="email"
              autocomplete="email"
              placeholder="nama@example.com"
              required
              :class="[
                'w-full rounded-lg border bg-surface px-4 py-3 text-sm text-ink',
                'placeholder:text-ink-muted outline-none',
                'transition-[border-color,box-shadow] duration-160 ease',
                'focus:border-accent focus:ring-3 focus:ring-[rgba(217,37,29,0.12)]',
                submitted && !form.email ? 'border-accent' : 'border-transparent',
              ]"
            />
          </div>

          <!-- Password -->
          <div class="mb-6">
            <label for="reg-password" class="mb-1 block text-sm font-medium text-ink">
              Password
            </label>
            <input
              id="reg-password"
              v-model="form.password"
              type="password"
              autocomplete="new-password"
              placeholder="Min. 8 karakter"
              required
              :class="[
                'w-full rounded-lg border bg-surface px-4 py-3 text-sm text-ink',
                'placeholder:text-ink-muted outline-none',
                'transition-[border-color,box-shadow] duration-160 ease',
                'focus:border-accent focus:ring-3 focus:ring-[rgba(217,37,29,0.12)]',
                submitted && !form.password ? 'border-accent' : 'border-transparent',
              ]"
            />
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="isPending"
            class="flex min-h-11 w-full items-center justify-center rounded-sm bg-accent px-4
                   text-sm font-semibold text-white
                   transition-colors duration-160 ease
                   hover:opacity-90
                   active:scale-[0.98]
                   disabled:cursor-not-allowed disabled:opacity-50
                   focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-[rgba(217,37,29,0.4)]"
          >
            <svg
              v-if="isPending"
              class="mr-2 h-4 w-4 animate-spin"
              viewBox="0 0 24 24"
              fill="none"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
            {{ isPending ? 'Mendaftar...' : 'Daftar Sekarang' }}
          </button>

        </form>
      </div>

      <!-- Login link -->
      <p class="mt-5 text-center text-sm text-ink-muted">
        Sudah punya akun?
        <button
          class="font-semibold text-accent hover:underline"
          @click="router.push({ name: 'login' })"
        >
          Masuk
        </button>
      </p>

    </div>
  </div>
</template>

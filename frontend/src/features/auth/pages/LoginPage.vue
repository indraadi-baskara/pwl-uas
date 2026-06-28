<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useLogin } from '../api/use-login'

const { mutate: login, isPending, isError, error } = useLogin()

const form = reactive({ email: '', password: '' })
const submitted = ref(false)

function handleSubmit() {
  submitted.value = true
  login({ email: form.email, password: form.password })
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-[#F7F6F4] px-4">
    <div class="w-full max-w-sm">

      <!-- Brand header -->
      <div class="mb-8 text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-[#D9251D]">
          Admin Panel
        </p>
        <h1
          class="mt-1 font-display text-3xl font-bold tracking-tight text-[#1C1C1E]"
          style="font-family: var(--font-display)"
        >
          Depo Waroeng Ban
        </h1>
        <p class="mt-1 text-sm text-[#6B6B70]">Masuk ke dasbor admin</p>
      </div>

      <!-- Card -->
      <div class="rounded-xl bg-white px-8 py-8 shadow-sm">

        <!-- Error banner -->
        <div
          v-if="isError"
          role="alert"
          class="mb-5 rounded-lg bg-[#F9EBEA] px-4 py-3 text-sm text-[#D9251D]"
        >
          {{ error?.message ?? 'Email atau password salah.' }}
        </div>

        <form novalidate @submit.prevent="handleSubmit">

          <!-- Email -->
          <div class="mb-4">
            <label for="email" class="mb-1 block text-sm font-medium text-[#1C1C1E]">
              Email
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              autocomplete="email"
              placeholder="admin@example.com"
              required
              :class="[
                'w-full rounded-lg border bg-[#EDEDEB] px-4 py-3 text-sm text-[#1C1C1E]',
                'placeholder:text-[#A8A8AD] outline-none',
                'transition-[border-color,box-shadow] duration-[160ms] ease',
                'focus:border-[#D9251D] focus:ring-3 focus:ring-[rgba(217,37,29,0.12)]',
                submitted && !form.email ? 'border-[#D9251D]' : 'border-transparent',
              ]"
            />
          </div>

          <!-- Password -->
          <div class="mb-6">
            <label for="password" class="mb-1 block text-sm font-medium text-[#1C1C1E]">
              Password
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              autocomplete="current-password"
              placeholder="••••••••"
              required
              :class="[
                'w-full rounded-lg border bg-[#EDEDEB] px-4 py-3 text-sm text-[#1C1C1E]',
                'placeholder:text-[#A8A8AD] outline-none',
                'transition-[border-color,box-shadow] duration-[160ms] ease',
                'focus:border-[#D9251D] focus:ring-3 focus:ring-[rgba(217,37,29,0.12)]',
                submitted && !form.password ? 'border-[#D9251D]' : 'border-transparent',
              ]"
            />
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="isPending"
            class="flex min-h-[44px] w-full items-center justify-center rounded-sm bg-[#D9251D] px-4
                   text-sm font-semibold text-white
                   transition-colors duration-[160ms] ease
                   hover:bg-[#B81E17]
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
            {{ isPending ? 'Memproses...' : 'Masuk ke Admin' }}
          </button>

        </form>
      </div>

    </div>
  </div>
</template>

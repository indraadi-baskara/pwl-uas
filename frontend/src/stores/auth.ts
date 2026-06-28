import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { type AuthUser, AuthUserSchema } from '@/features/auth/api/auth.schema'

export const useAuthStore = defineStore('auth', () => {
  const user        = ref<AuthUser | null>(null)
  const initialized = ref(false)

  const isAuthenticated = computed(() => user.value !== null)
  const isAdmin         = computed(() => user.value?.role === 'admin')

  function setUser(u: AuthUser) {
    user.value = u
  }

  function clearUser() {
    user.value = null
  }

  async function init() {
    try {
      // Raw fetch — must NOT go through apiClient's 401 interceptor,
      // which would trigger a refresh loop on an expired token.
      const res = await fetch(`${import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000'}/auth/me`, {
        credentials: 'include',
      })

      if (!res.ok) {
        user.value = null
        return
      }

      const json = await res.json() as { data: unknown }
      user.value = AuthUserSchema.parse(json.data)
    } catch {
      user.value = null
    } finally {
      initialized.value = true
    }
  }

  return { user, initialized, isAuthenticated, isAdmin, setUser, clearUser, init }
})

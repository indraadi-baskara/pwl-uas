import { useMutation } from '@tanstack/vue-query'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { postLogin } from './post-login'
import type { LoginRequest } from './auth.schema'

export function useLogin() {
  const auth   = useAuthStore()
  const router = useRouter()

  return useMutation({
    mutationFn: (credentials: LoginRequest) => postLogin(credentials),
    onSuccess: (user) => {
      auth.setUser(user)
      router.push({ name: 'admin' })
    },
  })
}

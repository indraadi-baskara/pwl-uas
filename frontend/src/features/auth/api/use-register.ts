import { useMutation } from '@tanstack/vue-query'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { postRegister } from './post-register'
import type { RegisterRequest } from './auth.schema'

export function useRegister() {
  const auth   = useAuthStore()
  const router = useRouter()

  return useMutation({
    mutationFn: (payload: RegisterRequest) => postRegister(payload),
    onSuccess: (user) => {
      auth.setUser(user)
      router.push({ name: user.role === 'admin' ? 'admin' : 'products' })
    },
  })
}

import { z } from 'zod'
import type { components } from '@/types/api.generated'

// Mirrors App\DTO\Auth\AuthUserResponse — bound via ZodType<T> so a PHP DTO
// shape change causes a compile error here until the schema is updated.
export type AuthUser = components['schemas']['AuthUserResponse']

export const AuthUserSchema: z.ZodType<AuthUser> = z.object({
  id:    z.number(),
  email: z.string().email(),
  role:  z.enum(['user', 'admin']),
})

export type LoginRequest = components['schemas']['LoginRequest']

export const LoginRequestSchema: z.ZodType<LoginRequest> = z.object({
  email:    z.string().email(),
  password: z.string().min(8),
})

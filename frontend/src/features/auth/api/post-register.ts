import { apiClient } from '@/lib/api-client'
import { AuthUserSchema, type AuthUser, type RegisterRequest } from './auth.schema'

export async function postRegister(payload: RegisterRequest): Promise<AuthUser> {
  const data = await apiClient.post<unknown>('/auth/register', payload)
  return AuthUserSchema.parse(data)
}

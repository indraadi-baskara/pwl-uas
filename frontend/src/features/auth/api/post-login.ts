import { apiClient } from '@/lib/api-client'
import { AuthUserSchema, type AuthUser, type LoginRequest } from './auth.schema'

export async function postLogin(credentials: LoginRequest): Promise<AuthUser> {
  const data = await apiClient.post<unknown>('/auth/login', credentials)
  return AuthUserSchema.parse(data)
}

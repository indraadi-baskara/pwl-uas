import { apiClient } from '@/lib/api-client'

export async function postLogout(): Promise<void> {
  await apiClient.post('/auth/logout', {})
}

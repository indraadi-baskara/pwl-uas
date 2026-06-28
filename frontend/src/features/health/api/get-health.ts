import { env } from '@/config/env'
import { type HealthResponse, HealthResponseSchema } from './health.schema'

export type { HealthResponse }

export async function getHealth(): Promise<HealthResponse> {
  const res = await fetch(`${env.apiBaseUrl}/health`)

  if (!res.ok) {
    throw new Error(`HTTP ${res.status}`)
  }

  return HealthResponseSchema.parse(await res.json())
}

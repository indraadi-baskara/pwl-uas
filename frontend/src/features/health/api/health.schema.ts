import { z } from 'zod'
import type { components } from '@/types/api.generated'

// Source of truth: generated from App\DTO\HealthResponse via openapi.json.
// The ZodType<T> constraint ensures this schema stays in sync with the generated type.
export type HealthResponse = components['schemas']['HealthResponse']

export const HealthResponseSchema: z.ZodType<HealthResponse> = z.object({
  status: z.string(),
})

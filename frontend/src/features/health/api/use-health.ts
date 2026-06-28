import { type UseQueryOptions, useQuery } from '@tanstack/vue-query'
import { getHealth, type HealthResponse } from './get-health'

type HealthQueryOptions = Omit<UseQueryOptions<HealthResponse>, 'queryKey' | 'queryFn'>

export function useHealth(options?: HealthQueryOptions) {
  return useQuery({
    queryKey: ['health'],
    queryFn: getHealth,
    ...options,
  })
}

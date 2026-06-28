import { computed, toValue, type MaybeRefOrGetter } from 'vue'
import { type UseQueryOptions, useQuery } from '@tanstack/vue-query'
import { getOrder } from './get-order'
import type { Order } from './order.schema'

type OrderQueryOptions = Omit<UseQueryOptions<Order>, 'queryKey' | 'queryFn'>

export function useOrder(id: MaybeRefOrGetter<number>, options?: OrderQueryOptions) {
  return useQuery({
    queryKey: computed(() => ['orders', toValue(id)]),
    queryFn:  () => getOrder(toValue(id)),
    ...options,
  })
}

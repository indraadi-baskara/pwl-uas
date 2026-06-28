import { computed, toValue, type MaybeRefOrGetter } from 'vue'
import { type UseQueryOptions, useQuery } from '@tanstack/vue-query'
import { getOrders, type Order, type Pagination } from './get-orders'

type OrderListResult    = { data: Order[]; pagination: Pagination }
type OrdersQueryOptions = Omit<UseQueryOptions<OrderListResult>, 'queryKey' | 'queryFn'>

export function useOrders(page?: MaybeRefOrGetter<number>, options?: OrdersQueryOptions) {
  return useQuery({
    queryKey: computed(() => ['orders', toValue(page) ?? 1]),
    queryFn:  () => getOrders(toValue(page) ?? 1),
    ...options,
  })
}

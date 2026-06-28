import { computed, toValue, type MaybeRefOrGetter } from 'vue'
import { type UseQueryOptions, useQuery } from '@tanstack/vue-query'
import { getProduct } from './get-product'
import type { Product } from './product.schema'

type ProductQueryOptions = Omit<UseQueryOptions<Product>, 'queryKey' | 'queryFn'>

export function useProduct(id: MaybeRefOrGetter<number>, options?: ProductQueryOptions) {
  return useQuery({
    queryKey: computed(() => ['products', toValue(id)]),
    queryFn:  () => getProduct(toValue(id)),
    ...options,
  })
}

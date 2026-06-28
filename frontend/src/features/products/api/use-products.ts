import { computed, toValue, type MaybeRefOrGetter } from 'vue'
import { type UseQueryOptions, useQuery } from '@tanstack/vue-query'
import { getProducts, type ProductFilters, type Pagination } from './get-products'
import type { Product } from './product.schema'

type ProductListResult = { data: Product[]; pagination: Pagination }
type ProductsQueryOptions = Omit<UseQueryOptions<ProductListResult>, 'queryKey' | 'queryFn'>

export function useProducts(filters?: MaybeRefOrGetter<ProductFilters>, options?: ProductsQueryOptions) {
  return useQuery({
    queryKey: computed(() => ['products', toValue(filters) ?? {}]),
    queryFn:  () => getProducts(toValue(filters) ?? {}),
    ...options,
  })
}

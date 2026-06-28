import { apiClient, type Pagination } from '@/lib/api-client'
import { type Product, ProductSchema } from './product.schema'

export type { Pagination }
export type { Product }

export type ProductFilters = {
  page?:      number
  limit?:     number
  search?:    string
  category?:  string
  min_price?: number
  max_price?: number
}

export async function getProducts(filters: ProductFilters = {}): Promise<{ data: Product[]; pagination: Pagination }> {
  const params = new URLSearchParams()

  if (filters.page)                            params.set('page',      String(filters.page))
  if (filters.limit)                           params.set('limit',     String(filters.limit))
  if (filters.search?.trim())                  params.set('search',    filters.search.trim())
  if (filters.category?.trim())                params.set('category',  filters.category.trim())
  if (filters.min_price != null)               params.set('min_price', String(filters.min_price))
  if (filters.max_price != null)               params.set('max_price', String(filters.max_price))

  const query  = params.toString()
  const result = await apiClient.getPaginated<Product>(`/products${query ? '?' + query : ''}`)

  return {
    data:       result.data.map(p => ProductSchema.parse(p)),
    pagination: result.pagination,
  }
}

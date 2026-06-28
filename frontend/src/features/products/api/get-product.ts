import { apiClient } from '@/lib/api-client'
import { type Product, ProductSchema } from './product.schema'

export type { Product }

export async function getProduct(id: number): Promise<Product> {
  const data = await apiClient.get<Product>(`/products/${id}`)
  return ProductSchema.parse(data)
}

import { apiClient } from '@/lib/api-client'
import { type Product, ProductSchema } from './product.schema'
import type { ProductFormData } from './post-product'

export type { ProductFormData }

export async function putProduct(id: number, payload: ProductFormData): Promise<Product> {
  const fd = new FormData()
  fd.append('name',  payload.name)
  fd.append('price', String(payload.price))
  if (payload.description) fd.append('description', payload.description)
  if (payload.stock != null) fd.append('stock',      String(payload.stock))
  if (payload.category)      fd.append('category',   payload.category)
  if (payload.image)         fd.append('image',      payload.image)

  const data = await apiClient.put<Product>(`/products/${id}`, fd)
  return ProductSchema.parse(data)
}

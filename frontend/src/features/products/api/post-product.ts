import { apiClient } from '@/lib/api-client'
import { type Product, ProductSchema } from './product.schema'

export type ProductFormData = {
  name:         string
  price:        number
  description?: string | null
  stock?:       number
  category?:    string | null
  image?:       File | null
}

export async function postProduct(payload: ProductFormData): Promise<Product> {
  const fd = new FormData()
  fd.append('name',  payload.name)
  fd.append('price', String(payload.price))
  if (payload.description) fd.append('description', payload.description)
  if (payload.stock != null) fd.append('stock',      String(payload.stock))
  if (payload.category)      fd.append('category',   payload.category)
  if (payload.image)         fd.append('image',      payload.image)

  const data = await apiClient.post<Product>('/products', fd)
  return ProductSchema.parse(data)
}

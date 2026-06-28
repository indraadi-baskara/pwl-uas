import { apiClient } from '@/lib/api-client'
import { type Cart, CartSchema } from './cart.schema'

export type AddCartItemPayload = {
  product_id: number
  quantity?:  number
}

export async function postCartItem(payload: AddCartItemPayload): Promise<Cart> {
  const data = await apiClient.post<Cart>('/cart/items', payload)
  return CartSchema.parse(data)
}

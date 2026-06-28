import { apiClient } from '@/lib/api-client'
import { type Cart, CartSchema } from './cart.schema'

export async function putCartItem(id: number, quantity: number): Promise<Cart> {
  const data = await apiClient.put<Cart>(`/cart/items/${id}`, { quantity })
  return CartSchema.parse(data)
}

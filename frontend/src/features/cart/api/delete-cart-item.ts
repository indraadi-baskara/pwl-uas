import { apiClient } from '@/lib/api-client'
import { type Cart, CartSchema } from './cart.schema'

export async function deleteCartItem(id: number): Promise<Cart> {
  const data = await apiClient.delete<Cart>(`/cart/items/${id}`)
  return CartSchema.parse(data)
}

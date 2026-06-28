import { apiClient } from '@/lib/api-client'
import { type Cart, CartSchema } from './cart.schema'

export async function deleteCart(): Promise<Cart> {
  const data = await apiClient.delete<Cart>('/cart')
  return CartSchema.parse(data)
}

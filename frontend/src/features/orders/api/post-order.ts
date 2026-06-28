import { apiClient } from '@/lib/api-client'
import { type Order, OrderSchema } from './order.schema'

export type { Order }

export async function postOrder(): Promise<Order> {
  const data = await apiClient.post<Order>('/orders', {})
  return OrderSchema.parse(data)
}

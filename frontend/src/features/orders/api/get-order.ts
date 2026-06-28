import { apiClient } from '@/lib/api-client'
import { type Order, OrderSchema } from './order.schema'

export type { Order }

export async function getOrder(id: number): Promise<Order> {
  const data = await apiClient.get<Order>(`/orders/${id}`)
  return OrderSchema.parse(data)
}

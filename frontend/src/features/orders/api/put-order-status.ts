import { apiClient } from '@/lib/api-client'
import { type Order, type OrderStatus, OrderSchema } from './order.schema'

export type { OrderStatus }

export async function putOrderStatus(id: number, status: OrderStatus): Promise<Order> {
  const data = await apiClient.put<Order>(`/orders/${id}/status`, { status })
  return OrderSchema.parse(data)
}

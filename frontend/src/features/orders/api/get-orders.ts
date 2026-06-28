import { apiClient, type Pagination } from '@/lib/api-client'
import { type Order, OrderSchema } from './order.schema'

export type { Order, Pagination }

export async function getOrders(page = 1, limit = 10): Promise<{ data: Order[]; pagination: Pagination }> {
  const result = await apiClient.getPaginated<Order>(`/orders?page=${page}&limit=${limit}`)

  return {
    data:       result.data.map(o => OrderSchema.parse(o)),
    pagination: result.pagination,
  }
}

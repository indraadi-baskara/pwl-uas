import { z } from 'zod'
import type { components } from '@/types/api.generated'

export type Order       = components['schemas']['Order']
export type OrderItem   = components['schemas']['OrderItem']
export type OrderStatus = Order['status']

const ORDER_STATUSES = ['pending', 'processing', 'shipped', 'completed', 'cancelled'] as const

export const OrderItemSchema: z.ZodType<OrderItem> = z.object({
  id:         z.number(),
  product_id: z.number().nullable().optional(),
  name:       z.string(),
  price:      z.number(),
  quantity:   z.number(),
  subtotal:   z.number(),
})

export const OrderSchema: z.ZodType<Order> = z.object({
  id:         z.number(),
  user_id:    z.number(),
  status:     z.enum(ORDER_STATUSES),
  total:      z.number(),
  items:      z.array(OrderItemSchema).optional(),
  created_at: z.string(),
})

export const STATUS_LABELS: Record<OrderStatus, string> = {
  pending:    'Menunggu',
  processing: 'Diproses',
  shipped:    'Dikirim',
  completed:  'Selesai',
  cancelled:  'Dibatalkan',
}

export const STATUS_COLORS: Record<OrderStatus, string> = {
  pending:    'bg-yellow-50 text-yellow-700',
  processing: 'bg-blue-50 text-blue-700',
  shipped:    'bg-indigo-50 text-indigo-700',
  completed:  'bg-green-50 text-green-700',
  cancelled:  'bg-accent-soft text-accent',
}

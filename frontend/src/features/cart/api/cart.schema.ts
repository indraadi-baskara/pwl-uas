import { z } from 'zod'
import type { components } from '@/types/api.generated'

export type Cart     = components['schemas']['Cart']
export type CartItem = components['schemas']['CartItem']

export const CartItemSchema: z.ZodType<CartItem> = z.object({
  id:                z.number(),
  product_id:        z.number(),
  product_name:      z.string(),
  product_image_url: z.string().nullable().optional(),
  price:             z.number(),
  quantity:          z.number(),
  subtotal:          z.number(),
})

export const CartSchema: z.ZodType<Cart> = z.object({
  id:    z.number(),
  items: z.array(CartItemSchema),
  total: z.number(),
})

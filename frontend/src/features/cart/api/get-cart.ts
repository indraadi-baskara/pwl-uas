import { env } from '@/config/env'
import { type Cart, CartSchema } from './cart.schema'

export type { Cart }

export async function getCart(): Promise<Cart | null> {
  const res = await fetch(`${env.apiBaseUrl}/cart`, { credentials: 'include' })

  if (res.status === 401) return null
  if (!res.ok) throw new Error(`HTTP ${res.status}`)

  const json = await res.json() as { data: unknown }
  return CartSchema.parse(json.data)
}

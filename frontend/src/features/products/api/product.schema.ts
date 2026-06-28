import { z } from 'zod'
import type { components } from '@/types/api.generated'

export type Product = components['schemas']['Product']
export type CreateProductRequest = components['schemas']['CreateProductRequest']

export const ProductSchema: z.ZodType<Product> = z.object({
  id:          z.number(),
  name:        z.string(),
  description: z.string().nullable().optional(),
  price:       z.number(),
  stock:       z.number(),
  category:    z.string().nullable().optional(),
  image_url:   z.string().nullable().optional(),
  created_at:  z.string().optional(),
})

export const CreateProductRequestSchema: z.ZodType<CreateProductRequest> = z.object({
  name:        z.string().min(1, 'Nama produk wajib diisi'),
  price:       z.number().min(0, 'Harga tidak boleh negatif'),
  description: z.string().nullable().optional(),
  stock:       z.number().min(0).optional(),
  category:    z.string().nullable().optional(),
})

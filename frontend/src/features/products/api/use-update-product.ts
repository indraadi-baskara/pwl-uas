import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { putProduct } from './put-product'
import type { ProductFormData } from './post-product'

export function useUpdateProduct(id: number) {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (payload: ProductFormData) => putProduct(id, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['products'] })
      queryClient.invalidateQueries({ queryKey: ['products', id] })
    },
  })
}

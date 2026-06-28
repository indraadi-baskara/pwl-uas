import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { postProduct } from './post-product'

export function useCreateProduct() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: postProduct,
    onSuccess:  () => { queryClient.invalidateQueries({ queryKey: ['products'] }) },
  })
}

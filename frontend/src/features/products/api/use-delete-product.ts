import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { deleteProduct } from './delete-product'

export function useDeleteProduct() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: deleteProduct,
    onSuccess:  () => { queryClient.invalidateQueries({ queryKey: ['products'] }) },
  })
}

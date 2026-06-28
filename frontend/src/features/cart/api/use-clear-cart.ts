import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { deleteCart } from './delete-cart'

export function useClearCart() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: deleteCart,
    onSuccess:  () => { queryClient.invalidateQueries({ queryKey: ['cart'] }) },
  })
}

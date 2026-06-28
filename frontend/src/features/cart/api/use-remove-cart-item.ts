import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { deleteCartItem } from './delete-cart-item'

export function useRemoveCartItem() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: deleteCartItem,
    onSuccess:  () => { queryClient.invalidateQueries({ queryKey: ['cart'] }) },
  })
}

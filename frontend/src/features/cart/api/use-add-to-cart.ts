import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { postCartItem } from './post-cart-item'

export function useAddToCart() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: postCartItem,
    onSuccess:  () => { queryClient.invalidateQueries({ queryKey: ['cart'] }) },
  })
}

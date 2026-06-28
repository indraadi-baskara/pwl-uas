import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { postOrder } from './post-order'

export function usePlaceOrder() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: postOrder,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['cart'] })
      queryClient.invalidateQueries({ queryKey: ['orders'] })
    },
  })
}

import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { putCartItem } from './put-cart-item'

export function useUpdateCartItem() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: ({ id, quantity }: { id: number; quantity: number }) =>
      putCartItem(id, quantity),
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['cart'] }) },
  })
}

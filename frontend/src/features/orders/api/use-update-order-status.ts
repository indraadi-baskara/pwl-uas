import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { putOrderStatus } from './put-order-status'
import type { OrderStatus } from './order.schema'

export function useUpdateOrderStatus(id: number) {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (status: OrderStatus) => putOrderStatus(id, status),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['orders'] })
      queryClient.invalidateQueries({ queryKey: ['orders', id] })
    },
  })
}

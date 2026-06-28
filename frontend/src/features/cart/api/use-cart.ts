import { watch } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { getCart } from './get-cart'
import { useCartStore } from '@/stores/cart'

export function useCart() {
  const cartStore = useCartStore()

  const query = useQuery({
    queryKey: ['cart'],
    queryFn:  getCart,
  })

  watch(query.data, (data) => {
    if (data) cartStore.setFromCart(data)
    else cartStore.reset()
  }, { immediate: true })

  return query
}

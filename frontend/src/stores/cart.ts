import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Cart } from '@/features/cart/api/cart.schema'

export const useCartStore = defineStore('cart', () => {
  const itemCount = ref(0)
  const total     = ref(0)

  function setFromCart(cart: Cart) {
    itemCount.value = cart.items.reduce((sum, i) => sum + i.quantity, 0)
    total.value     = cart.total
  }

  function reset() {
    itemCount.value = 0
    total.value     = 0
  }

  return { itemCount, total, setFromCart, reset }
})

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AdminHeader from '@/components/AdminHeader.vue'
import { useDeleteProduct } from '@/features/products/api/use-delete-product'
import type { Product } from '@/features/products/api/product.schema'
import { useProducts } from '@/features/products/api/use-products'
import Pagination from '@/features/products/components/Pagination.vue'
import ProductCard from '@/features/products/components/ProductCard.vue'

const router = useRouter()
const page   = ref(1)
const filters = { limit: 12 }

const { data, isLoading }         = useProducts(() => ({ ...filters, page: page.value }))
const { mutate: remove, isPending: deleting } = useDeleteProduct()

const confirmTarget = ref<Product | null>(null)

function onEdit(product: Product) {
  void router.push({ name: 'admin-products-edit', params: { id: product.id } })
}

function onDelete(product: Product) {
  confirmTarget.value = product
}

function confirmDelete() {
  if (!confirmTarget.value) return
  const id = confirmTarget.value.id
  confirmTarget.value = null
  remove(id)
}

function onPageChange(p: number) {
  page.value = p
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<template>
  <div class="min-h-screen bg-canvas">

    <AdminHeader back="Dasbor" back-route="admin" title="Kelola Produk">
      <template #action>
        <button
          class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white
                 transition-opacity duration-160 hover:opacity-90"
          @click="router.push({ name: 'admin-products-create' })"
        >
          + Tambah Produk
        </button>
      </template>
    </AdminHeader>

    <main class="mx-auto max-w-6xl px-6 py-8">

      <!-- Loading skeleton -->
      <div v-if="isLoading" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        <div
          v-for="n in 8"
          :key="n"
          class="animate-pulse rounded-lg border border-surface bg-white"
        >
          <div class="aspect-square bg-surface" />
          <div class="p-4 space-y-2">
            <div class="h-3 w-16 rounded bg-surface" />
            <div class="h-4 w-full rounded bg-surface" />
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div
        v-else-if="!data?.data.length"
        class="rounded-xl border border-surface bg-white px-6 py-16 text-center"
      >
        <p class="text-ink-muted">Belum ada produk. Tambahkan produk pertama!</p>
        <button
          class="mt-4 rounded-lg bg-accent px-5 py-2 text-sm font-semibold text-white
                 transition-opacity duration-160 hover:opacity-90"
          @click="router.push({ name: 'admin-products-create' })"
        >
          + Tambah Produk
        </button>
      </div>

      <!-- Grid -->
      <div v-else>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
          <ProductCard
            v-for="product in data.data"
            :key="product.id"
            :product="product"
            :admin="true"
            @edit="onEdit"
            @delete="onDelete"
          />
        </div>
        <div class="mt-8 flex justify-center">
          <Pagination :pagination="data.pagination" @change="onPageChange" />
        </div>
      </div>

    </main>

    <!-- Delete confirmation dialog -->
    <Teleport to="body">
      <div
        v-if="confirmTarget"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
        @click.self="confirmTarget = null"
      >
        <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl">
          <h2 class="font-bold text-ink" style="font-family: var(--font-display)">
            Hapus Produk?
          </h2>
          <p class="mt-2 text-sm text-ink-muted">
            "{{ confirmTarget.name }}" akan dihapus secara permanen.
          </p>
          <div class="mt-5 flex gap-3">
            <button
              :disabled="deleting"
              class="flex-1 rounded-lg bg-accent py-2 text-sm font-semibold text-white
                     transition-opacity duration-160 disabled:opacity-50 enabled:hover:opacity-90"
              @click="confirmDelete"
            >
              Hapus
            </button>
            <button
              class="flex-1 rounded-lg border border-surface py-2 text-sm text-ink-muted
                     transition-colors duration-160 hover:border-ink"
              @click="confirmTarget = null"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

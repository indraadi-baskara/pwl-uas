<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminHeader from '@/components/AdminHeader.vue'
import type { ProductFormData } from '@/features/products/api/post-product'
import { useProduct } from '@/features/products/api/use-product'
import { useUpdateProduct } from '@/features/products/api/use-update-product'
import ProductForm from '@/features/products/components/ProductForm.vue'

const route  = useRoute()
const router = useRouter()
const id     = computed(() => Number(route.params.id))

const { data: product, isLoading } = useProduct(id)
const { mutate, isPending, error }  = useUpdateProduct(id.value)

function onSubmit(data: ProductFormData) {
  mutate(data, {
    onSuccess: () => { void router.push({ name: 'admin-products' }) },
  })
}
</script>

<template>
  <div class="min-h-screen bg-canvas">

    <AdminHeader back="Kembali" back-route="admin-products" title="Edit Produk" />

    <main class="mx-auto max-w-3xl px-6 py-8">

      <!-- Loading -->
      <div v-if="isLoading" class="animate-pulse rounded-xl border border-surface bg-white p-6">
        <div class="space-y-4">
          <div v-for="n in 5" :key="n" class="h-10 rounded bg-surface" />
        </div>
      </div>

      <!-- Form -->
      <div v-else-if="product" class="rounded-xl border border-surface bg-white p-6">
        <p v-if="error" class="mb-4 rounded bg-accent-soft px-4 py-2 text-sm text-accent">
          {{ (error as Error).message }}
        </p>
        <ProductForm
          :initial="product"
          submit-label="Simpan Perubahan"
          :loading="isPending"
          @submit="onSubmit"
          @cancel="router.push({ name: 'admin-products' })"
        />
      </div>

    </main>

  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import AdminHeader from '@/components/AdminHeader.vue'
import type { ProductFormData } from '@/features/products/api/post-product'
import { useCreateProduct } from '@/features/products/api/use-create-product'
import ProductForm from '@/features/products/components/ProductForm.vue'

const router = useRouter()
const { mutate, isPending, error } = useCreateProduct()

function onSubmit(data: ProductFormData) {
  mutate(data, {
    onSuccess: () => { void router.push({ name: 'admin-products' }) },
  })
}
</script>

<template>
  <div class="min-h-screen bg-canvas">

    <AdminHeader back="Kembali" back-route="admin-products" title="Tambah Produk" />

    <main class="mx-auto max-w-3xl px-6 py-8">
      <div class="rounded-xl border border-surface bg-white p-6">
        <p v-if="error" class="mb-4 rounded bg-accent-soft px-4 py-2 text-sm text-accent">
          {{ (error as Error).message }}
        </p>
        <ProductForm
          submit-label="Tambah Produk"
          :loading="isPending"
          @submit="onSubmit"
          @cancel="router.push({ name: 'admin-products' })"
        />
      </div>
    </main>

  </div>
</template>

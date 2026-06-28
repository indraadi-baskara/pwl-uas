<script setup lang="ts">
import { ref, watch } from 'vue'
import type { Product } from '@/features/products/api/product.schema'
import type { ProductFormData } from '@/features/products/api/post-product'

const props = defineProps<{
  initial?:   Product
  loading?:   boolean
  submitLabel?: string
}>()

const emit = defineEmits<{
  submit: [data: ProductFormData]
  cancel: []
}>()

const name        = ref(props.initial?.name ?? '')
const price       = ref(props.initial?.price ?? 0)
const stock       = ref(props.initial?.stock ?? 0)
const category    = ref(props.initial?.category ?? '')
const description = ref(props.initial?.description ?? '')
const imageFile   = ref<File | null>(null)
const imagePreview = ref<string | null>(props.initial?.image_url ?? null)
const error       = ref('')

watch(() => props.initial, (val) => {
  if (!val) return
  name.value        = val.name
  price.value       = val.price
  stock.value       = val.stock
  category.value    = val.category ?? ''
  description.value = val.description ?? ''
  imagePreview.value = val.image_url ?? null
}, { immediate: true })

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement
  const file  = input.files?.[0] ?? null
  imageFile.value = file

  if (file) {
    const reader = new FileReader()
    reader.onload = () => { imagePreview.value = reader.result as string }
    reader.readAsDataURL(file)
  }
}

function handleSubmit() {
  error.value = ''

  if (!name.value.trim()) {
    error.value = 'Nama produk wajib diisi'
    return
  }
  if (price.value < 0) {
    error.value = 'Harga tidak boleh negatif'
    return
  }

  emit('submit', {
    name:        name.value.trim(),
    price:       price.value,
    stock:       stock.value,
    category:    category.value.trim() || null,
    description: description.value.trim() || null,
    image:       imageFile.value,
  })
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="handleSubmit">
    <!-- Error -->
    <p v-if="error" class="rounded bg-accent-soft px-4 py-2 text-sm text-accent">
      {{ error }}
    </p>

    <!-- Name -->
    <div>
      <label class="mb-1 block text-sm font-medium text-ink">
        Nama Produk <span class="text-accent">*</span>
      </label>
      <input
        v-model="name"
        type="text"
        placeholder="Contoh: Ban Motor IRC"
        class="w-full rounded-lg border border-surface bg-white px-3 py-2 text-sm text-ink
               placeholder-ink-muted outline-none transition-colors duration-160
               focus:border-ink"
      />
    </div>

    <!-- Price + Stock row -->
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="mb-1 block text-sm font-medium text-ink">
          Harga (Rp) <span class="text-accent">*</span>
        </label>
        <input
          v-model.number="price"
          type="number"
          min="0"
          placeholder="75000"
          class="w-full rounded-lg border border-surface bg-white px-3 py-2 text-sm text-ink
                 placeholder-ink-muted outline-none transition-colors duration-160
                 focus:border-ink"
        />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-ink">Stok</label>
        <input
          v-model.number="stock"
          type="number"
          min="0"
          placeholder="0"
          class="w-full rounded-lg border border-surface bg-white px-3 py-2 text-sm text-ink
                 placeholder-ink-muted outline-none transition-colors duration-160
                 focus:border-ink"
        />
      </div>
    </div>

    <!-- Category -->
    <div>
      <label class="mb-1 block text-sm font-medium text-ink">Kategori</label>
      <input
        v-model="category"
        type="text"
        placeholder="Contoh: motor, mobil, truk"
        class="w-full rounded-lg border border-surface bg-white px-3 py-2 text-sm text-ink
               placeholder-ink-muted outline-none transition-colors duration-160
               focus:border-ink"
      />
    </div>

    <!-- Description -->
    <div>
      <label class="mb-1 block text-sm font-medium text-ink">Deskripsi</label>
      <textarea
        v-model="description"
        rows="3"
        placeholder="Deskripsi singkat produk..."
        class="w-full rounded-lg border border-surface bg-white px-3 py-2 text-sm text-ink
               placeholder-ink-muted outline-none transition-colors duration-160
               focus:border-ink resize-none"
      />
    </div>

    <!-- Image upload -->
    <div>
      <label class="mb-1 block text-sm font-medium text-ink">Gambar Produk</label>
      <div class="flex items-start gap-4">
        <div
          v-if="imagePreview"
          class="h-20 w-20 shrink-0 overflow-hidden rounded-lg border border-surface"
        >
          <img :src="imagePreview" alt="Preview" class="h-full w-full object-cover" />
        </div>
        <label
          class="flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-surface
                 px-4 py-3 text-sm text-ink-muted transition-colors duration-160
                 hover:border-ink hover:text-ink"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0
                 0L8 8m4-4v12" />
          </svg>
          {{ imageFile ? imageFile.name : 'Pilih gambar (JPEG, PNG, WebP)' }}
          <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onFileChange" />
        </label>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-3 pt-2">
      <button
        type="submit"
        :disabled="loading"
        class="rounded-lg bg-accent px-6 py-2 text-sm font-semibold text-white
               transition-opacity duration-160 disabled:opacity-50 enabled:hover:opacity-90"
      >
        {{ submitLabel ?? 'Simpan' }}
      </button>
      <button
        type="button"
        class="rounded-lg border border-surface px-6 py-2 text-sm text-ink-muted
               transition-colors duration-160 hover:border-ink hover:text-ink"
        @click="emit('cancel')"
      >
        Batal
      </button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useHealth } from "../api/use-health";

const { data, isPending, isError } = useHealth({ retry: 1 });

const isOk = computed(() => !isError.value && data.value?.status === "ok");
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50">
    <!-- Loading -->
    <div
      v-if="isPending"
      class="flex flex-col items-center gap-3 text-gray-400"
    >
      <svg class="h-8 w-8 animate-spin" viewBox="0 0 24 24" fill="none">
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        />
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
        />
      </svg>
      <span class="text-sm tracking-wide">checking api...</span>
    </div>

    <!-- OK -->
    <div v-else-if="isOk" class="text-center">
      <p class="text-6xl font-black tracking-tight text-emerald-500">
        Hello World
      </p>
      <p class="mt-3 text-sm text-gray-400">API is healthy</p>
    </div>

    <!-- Error -->
    <div v-else class="text-center">
      <p class="text-6xl font-black tracking-tight text-rose-400">
        uh ohh ....
      </p>
      <p class="mt-3 text-sm text-gray-400">API is unreachable or unhealthy</p>
    </div>
  </div>
</template>

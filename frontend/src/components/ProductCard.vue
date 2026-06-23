<script setup>
import { computed } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['sell-product', 'view-recipe'])

const shelfLifeStatus = computed(() => {
  const hours = props.product.shelf_life_hours
  if (hours <= 8) return { label: 'Very short shelf life', class: 'bg-red-50 text-red-600', icon: '🔴' }
  if (hours <= 12) return { label: 'Sells fast — prioritize', class: 'bg-amber-50 text-amber-600', icon: '🟡' }
  if (hours <= 24) return { label: 'Sell today', class: 'bg-orange-50 text-orange-700', icon: '🟠' }
  return { label: 'Multi-day shelf life', class: 'bg-green-50 text-green-600', icon: '🟢' }
})

const formattedPrice = computed(() => {
  return props.product.selling_price.toLocaleString()
})

// Category badge colors using Tailwind classes
const categoryClass = computed(() => {
  const colors = {
    bread:  'bg-amber-100 text-amber-800',
    cake:   'bg-pink-100 text-pink-800',
    pastry: 'bg-indigo-100 text-indigo-800',
    bun:    'bg-emerald-100 text-emerald-800',
  }
  return colors[props.product.category.name] || 'bg-gray-100 text-gray-800'
})

function handleSell() {
  emit('sell-product', props.product)
}

function handleViewRecipe() {
  emit('view-recipe', props.product.id)
}
</script>

<template>
  <div
    class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-blue-600
           hover:-translate-y-0.5 hover:shadow-md transition-all duration-200
           flex flex-col gap-3"
    :class="{ 'opacity-60 border-gray-400': !product.is_active }"
  >
    <!-- Header: name + category -->
    <div class="flex justify-between items-start">
      <h3 class="text-lg font-semibold text-[#1A1A2E]">{{ product.name }}</h3>
      <span
        class="px-3 py-0.5 rounded-full text-xs font-medium capitalize whitespace-nowrap"
        :class="categoryClass"
      >
        {{ product.category.name }}
      </span>
    </div>

    <!-- Price -->
    <div class="flex items-baseline gap-2">
      <span class="text-2xl font-bold text-[#E8541E]">KES {{ formattedPrice }}</span>
      <span class="text-sm text-gray-400">per {{ product.unit }}</span>
    </div>

    <!-- Shelf life -->
    <div
      class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-md"
      :class="shelfLifeStatus.class"
    >
      <span>{{ shelfLifeStatus.icon }}</span>
      <span>{{ shelfLifeStatus.label }} ({{ product.shelf_life_hours }}h)</span>
    </div>

    <!-- Inactive warning -->
    <div v-if="!product.is_active"
      class="bg-red-50 text-red-800 px-3 py-2 rounded-md text-sm font-medium">
      ⚠ Product inactive — not available for sale
    </div>

    <!-- Actions -->
    <div class="flex gap-2 mt-auto">
      <button
        @click="handleSell"
        :disabled="!product.is_active"
        class="flex-1 py-2 px-4 bg-[#1A1A2E] text-white rounded-lg text-sm font-medium
               hover:bg-[#E8541E] transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
      >
        Sell
      </button>
      <button
        @click="handleViewRecipe"
        class="flex-1 py-2 px-4 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium
               hover:bg-gray-200 transition-colors"
      >
        View Recipe
      </button>
    </div>
  </div>
</template>
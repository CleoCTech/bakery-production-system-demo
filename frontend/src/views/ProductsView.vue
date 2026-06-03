<script setup>
import { ref, computed } from 'vue'
import ProductCard from '../components/ProductCard.vue'
import { useProductStore } from '../stores/productStore'


const productStore = useProductStore()


const searchQuery = ref('')
const selectedCategory = ref('all')

const filteredProducts = computed(() => {
  return productStore.products.filter(product => {
    const matchesSearch = product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = selectedCategory.value === 'all' || product.category === selectedCategory.value
    return matchesSearch && matchesCategory
  })
})



function handleSale(product) {
  alert(`Sale: ${product.name} for KES ${product.selling_price}`)
}

function handleViewRecipe(productId) {
  alert(`View recipe for product #${productId}`)
}
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-[#1A1A2E]">Product Catalog</h1>
      <p class="text-gray-500 text-sm mt-1">
        {{ filteredProducts.length }} of {{ productStore.products.length }} products ({{ productStore.activeProducts.length }} active)
      </p>
    </div>

    <!-- Search + filter -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
      <div class="relative w-full sm:max-w-xs">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search products..."
          class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm
                 focus:outline-none focus:border-[#E8541E] focus:ring-2 focus:ring-orange-100 transition"
        >
      </div>

      <div class="flex flex-wrap gap-2">
        <button
          v-for="cat in productStore.categories"
          :key="cat"
          @click="selectedCategory = cat"
          class="px-3.5 py-1.5 rounded-full text-sm font-medium capitalize border transition-colors"
          :class="selectedCategory === cat
            ? 'bg-[#1A1A2E] text-white border-[#1A1A2E]'
            : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400'"
        >
          {{ cat === 'all' ? 'All' : cat }}
        </button>
      </div>
    </div>

    <!-- Product grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <ProductCard
        v-for="product in filteredProducts"
        :key="product.id"
        :product="product"
        @sell-product="handleSale"
        @view-recipe="handleViewRecipe"
      />
    </div>

    <!-- Empty state -->
    <div v-if="filteredProducts.length === 0" class="text-center py-12 text-gray-400">
      No products match your search. Try a different term or category.
    </div>
  </div>
</template>
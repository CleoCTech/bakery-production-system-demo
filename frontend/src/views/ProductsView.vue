<script setup>
import { ref, computed, onMounted } from 'vue'
import ProductCard from '../components/ProductCard.vue'
import { useProductStore } from '../stores/productStore'


const productStore = useProductStore()


const searchQuery = ref('')
const selectedCategory = ref('all')

// FETCH products when the page loads — this replaces the hardcoded array!
onMounted(() => {
  productStore.fetchProducts()
})

const filteredProducts = computed(() => {
  return productStore.products.filter(product => {
    const matchesSearch = product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = selectedCategory.value === 'all'
      || product.category?.name === selectedCategory.value
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
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-[#1A1A2E]">Product Catalog</h1>
      <p class="text-gray-500 text-sm mt-1">
        {{ filteredProducts.length }} of {{ productStore.productCount }} products
      </p>
    </div>

    <!-- Loading state -->
    <div v-if="productStore.isLoading" class="flex flex-col items-center py-16">
      <div class="w-10 h-10 border-4 border-gray-200 border-t-[#E8541E] rounded-full animate-spin mb-4"></div>
      <p class="text-gray-500">Loading products from database...</p>
    </div>

    <!-- Error state -->
    <div v-else-if="productStore.error"
         class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
      <p class="font-medium">{{ productStore.error }}</p>
      <button @click="productStore.fetchProducts()"
              class="mt-2 text-sm text-red-600 underline">Try again</button>
    </div>

    <!-- Data loaded -->
    <template v-else>
      <!-- Search + filter -->
      <div class="flex gap-3 mb-6">
        <input v-model="searchQuery" type="text" placeholder="Search products..."
          class="flex-1 max-w-xs px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        <select v-model="selectedCategory"
          class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white capitalize
                 focus:outline-none focus:border-blue-500">
          <option value="all">All categories</option>
          <option v-for="cat in productStore.categories" :key="cat" :value="cat">
            {{ cat }}
          </option>
        </select>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <ProductCard
          v-for="product in filteredProducts"
          :key="product.id"
          :product="product"
          @sell-product="handleSale"
          @view-recipe="handleViewRecipe"
        />
      </div>

      <div v-if="filteredProducts.length === 0" class="text-center py-12 text-gray-400">
        No products match your search.
      </div>
    </template>
  </div>
</template>
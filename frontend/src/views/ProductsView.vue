<script setup>
//fetching all products from the backend
import { ref, computed } from 'vue'
import ProductCard from '../components/ProductCard.vue'

// ---------------------------------------------------------------
// SAMPLE DATA — matches our PRODUCTS table from the ERD
//
// In Week 6, this entire array will be replaced with:
//   onMounted(async () => {
//     const response = await axios.get('/api/products')
//     products.value = response.data
//   })
//
// The API response will return the EXACT same shape — that's why
// we use the same field names as the database columns now.
// ---------------------------------------------------------------
const products = ref([
  { id: 1, name: 'White Bread', category: 'bread', selling_price: 60, shelf_life_hours: 24, unit: 'loaf', is_active: true },
  { id: 2, name: 'Chocolate Cake', category: 'cake', selling_price: 350, shelf_life_hours: 72, unit: 'piece', is_active: true },
  { id: 3, name: 'Mandazi', category: 'bun', selling_price: 10, shelf_life_hours: 12, unit: 'piece', is_active: true },

])
// ad 4 more products to have more data to work with

// Search and filter state
const searchQuery = ref('')
const selectedCategory = ref('all')

// ---------------------------------------------------------------
// COMPUTED: these recalculate automatically when data changes
// This is the same pattern you'll use on every page
// ---------------------------------------------------------------
const categories = computed(() => {
  const cats = [...new Set(products.value.map(p => p.category))]
  return ['all', ...cats.sort()]
})

const filteredProducts = computed(() => {
  return products.value.filter(product => {
    const matchesSearch = product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = selectedCategory.value === 'all' || product.category === selectedCategory.value
    return matchesSearch && matchesCategory
  })
})

const activeCount = computed(() => products.value.filter(p => p.is_active).length)

// Event handlers
function handleSale(product) {
  alert(`Sale: ${product.name} for KES ${product.selling_price}`)
  // In Week 6: await axios.post('/api/sales', { product_id: product.id, quantity: 1, ... })
}

function handleViewRecipe(productId) {
  alert(`View recipe for product #${productId}`)
  // In Week 6: await axios.get(`/api/products/${productId}/recipe`)
  // This will return RECIPE_ITEMS joined with INGREDIENTS
}
</script>


<template>
  <div class="products-page">
    <div class="page-header">
      <div>
        <h1>Product Catalog</h1>
        <p class="subtitle">
          {{ filteredProducts.length }} of {{ products.length }} products
          ({{ activeCount }} active)
        </p>
      </div>
    </div>

    <!-- Search and filter bar -->
    <div class="filter-bar">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search products..."
        class="search-input"
      >
      <select v-model="selectedCategory" class="filter-select">
        <option v-for="cat in categories" :key="cat" :value="cat">
          {{ cat === 'all' ? 'All categories' : cat }}
        </option>
      </select>
    </div>

    <!-- Product grid -->
    <div class="product-grid">
      <ProductCard
        v-for="product in filteredProducts"
        :key="product.id"
        :product="product"
        @sell-product="handleSale"
        @view-recipe="handleViewRecipe"
      />
    </div>

    <!-- Empty state -->
    <div v-if="filteredProducts.length === 0" class="empty-state">
      <p>No products match your search. Try a different term or category.</p>
    </div>
  </div>
</template>

<style scoped>
.products-page {
  max-width: 1100px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.page-header h1 {
  font-size: 1.5rem;
  color: #1A1A2E;
  margin: 0;
}

.subtitle {
  color: #6B7280;
  font-size: 0.9rem;
  margin-top: 0.25rem;
}

.filter-bar {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.search-input {
  flex: 1;
  max-width: 300px;
  padding: 10px 14px;
  border: 1px solid #D1D5DB;
  border-radius: 8px;
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.2s;
}

.search-input:focus { border-color: #2563EB; }

.filter-select {
  padding: 10px 14px;
  border: 1px solid #D1D5DB;
  border-radius: 8px;
  font-size: 0.9rem;
  background: white;
  text-transform: capitalize;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: #9CA3AF;
  font-size: 1rem;
}
</style>
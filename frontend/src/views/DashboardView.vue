<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import StockIndicator from '../components/StockIndicator.vue'
import BatchCard from '../components/BatchCard.vue'
import { useIngredientStore } from '../stores/ingredientStore'
import { useBatchStore } from '../stores/batchStore'
import { useProductStore } from '../stores/productStore'
import api from '../api/axios'

const ingredientStore = useIngredientStore()
const batchStore = useBatchStore()
const productStore = useProductStore()

// Fetch all data when the page loads.
// allSettled (not all) so one failing store can't blank the whole dashboard —
// each section renders whatever data it successfully loaded.
onMounted(async () => {
  const results = await Promise.allSettled([
    productStore.fetchProducts(),
    ingredientStore.fetchIngredients(),
    batchStore.fetchBatches(),
  ])

  results
    .filter((r) => r.status === 'rejected')
    .forEach((r) => console.error('Dashboard load error:', r.reason))
})

// Auto-refresh every 60 seconds (live dashboard)

let refreshInterval

onMounted(() => {
  refreshInterval = setInterval(async () => {
    await ingredientStore.fetchIngredients()
    await batchStore.fetchBatches()
  }, 60000)
})

onUnmounted(() => {
  clearInterval(refreshInterval)
})


// Handle batch advancement — after advancing, re-fetch ingredients
// because stock may have been deducted on the server
async function handleAdvanceBatch(batchId) {
  try {
    await batchStore.advanceBatch(batchId)
    // Re-fetch ingredients to show updated stock levels
    await ingredientStore.fetchIngredients()
  } catch (err) {
    // Error already handled in batchStore (shows alert)
  }
}


</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-[#1A1A2E]">Baker's Dashboard</h1>
      <p class="text-gray-500 text-sm mt-1">Today's production overview</p>
    </div>

    <!-- Summary stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-8">
      <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Inventory Value</p>
        <p class="text-2xl font-bold text-[#1A1A2E] mt-1">KES {{ ingredientStore.totalStockValue }}</p>
      </div>
     <div class="bg-white rounded-xl p-4 shadow-sm text-center">
        <span class="block text-3xl font-bold text-[#1A1A2E]">{{ productStore.productCount }}</span>
        <span class="text-sm text-gray-500">Products</span>
      </div>

      <div
        class="bg-white rounded-xl p-4 shadow-sm border"
        :class="ingredientStore.lowStockCount > 0 ? 'border-red-200 bg-red-50' : 'border-gray-100'"
      >
        <p class="text-xs font-medium uppercase tracking-wide"
           :class="ingredientStore.lowStockCount > 0 ? 'text-red-500' : 'text-gray-400'">
          Needs Reorder
        </p>
        <p class="text-2xl font-bold mt-1"
           :class="needsReorder > 0 ? 'text-red-600' : 'text-[#1A1A2E]'">
          {{ ingredientStore.lowStockCount }}  
        </p>
      </div>


      <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Well Stocked</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ ingredientStore.lowStockCount }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">In Progress</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ batchStore.inProgressBatches }}</p>
      </div>
      <!-- <div
        class="bg-white rounded-xl p-4 shadow-sm border col-span-2 sm:col-span-1"
        :class="lowYield > 0 ? 'border-red-200 bg-red-50' : 'border-gray-100'"
      >
        <p class="text-xs font-medium uppercase tracking-wide"
           :class="lowYield > 0 ? 'text-red-500' : 'text-gray-400'">
          Low Yield
        </p>
        <p class="text-2xl font-bold mt-1"
           :class="lowYield > 0 ? 'text-red-600' : 'text-[#1A1A2E]'">
          {{ lowYield }}
        </p>
      </div> -->
    </div>

    <!-- Ingredient stock -->
    <h2 class="text-lg font-semibold text-[#1A1A2E] mb-3">Ingredient Stock</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <StockIndicator
        v-for="ing in ingredientStore.ingredients"
        :key="ing.id"
        :name="ing.name"
        :current="ing.current_stock"
        :reorder="ing.reorder_level"
        :unit="ing.unit"
        :cost-per-unit="ing.cost_per_unit"
      />
    </div>

    <!-- Production batches -->
    <h2 class="text-lg font-semibold text-[#1A1A2E] mt-8 mb-3">Production Batches</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <BatchCard
        v-for="batch in batchStore.batches"
        :key="batch.id"
        :batch="batch"
        @advance-batch="handleAdvanceBatch"
      />
    </div>

    <div v-if="batchStore.batches.length === 0" class="text-center py-8 text-gray-400">
        No production batches today. Plan a new batch to get started.
    </div>
  </div>
</template>

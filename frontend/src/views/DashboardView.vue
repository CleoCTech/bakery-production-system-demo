<script setup>
import { ref, computed, onMounted } from 'vue'
import StockIndicator from '../components/StockIndicator.vue'
import BatchCard from '../components/BatchCard.vue'
import { useIngredientStore } from '../stores/ingredientStore'
import { useBatchStore } from '../stores/batchStore'
import { useProductStore } from '../stores/productStore'
import api from '../api/axios'

const ingredientStore = useIngredientStore()
const batchStore = useBatchStore()
const productStore = useProductStore()


onMounted(async () => {
  try
  {
    const response = await api.get('health')
    console.log('API health check response:', response.data)
  } catch (error) {
    console.error('Error fetching data:', error)
  }
})


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
        <p class="text-2xl font-bold text-[#1A1A2E] mt-1">KES {{ ingredientStore.totalValue }}</p>
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
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ batchStore.inProgressCount }}</p>
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
        @advance-batch="batchStore.advanceBatch"
      />
    </div>

    <!-- Completion panel (Task 3) -->
    <!-- <div v-if="completingBatch"
         class="mt-4 bg-white rounded-xl p-5 shadow-sm border border-blue-200">
      <h3 class="text-base font-semibold text-[#1A1A2E]">
        Complete batch — {{ completingBatch.product_name }}
      </h3>
      <p class="text-sm text-gray-500 mt-1 mb-4">
        Record the actual output and wastage to finish this batch
        (planned {{ completingBatch.planned_quantity }} units).
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <label class="flex flex-col gap-1 text-sm">
          <span class="font-medium text-gray-700">Actual quantity</span>
          <input
            v-model.number="completionForm.actual"
            type="number"
            min="0"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm
                   focus:outline-none focus:border-[#E8541E] focus:ring-2 focus:ring-orange-100 transition"
          >
        </label>
        <label class="flex flex-col gap-1 text-sm">
          <span class="font-medium text-gray-700">Wastage</span>
          <input
            v-model.number="completionForm.wastage"
            type="number"
            min="0"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm
                   focus:outline-none focus:border-[#E8541E] focus:ring-2 focus:ring-orange-100 transition"
          >
        </label>
      </div>

      <div class="flex gap-2 mt-4">
        <button
          @click="confirmCompletion"
          class="py-2 px-5 bg-[#1A1A2E] text-white rounded-lg text-sm font-medium
                 hover:bg-[#E8541E] transition-colors"
        >
          Confirm
        </button>
        <button
          @click="cancelCompletion"
          class="py-2 px-5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium
                 hover:bg-gray-200 transition-colors"
        >
          Cancel
        </button>
      </div>
    </div> -->
  </div>
</template>

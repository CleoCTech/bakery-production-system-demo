<script setup>
import { ref, computed } from 'vue'
import StockIndicator from '../components/StockIndicator.vue'

const ingredients = ref([
  { id: 1, name: 'Wheat Flour', current_stock: 50, reorder_level: 20, unit: 'kg', cost_per_unit: 100 },
  { id: 2, name: 'Cinnamon', current_stock: 0.4, reorder_level: 0.5, unit: 'kg', cost_per_unit: 1200 },
  { id: 3, name: 'Eggs', current_stock: 120, reorder_level: 50, unit: 'pcs', cost_per_unit: 15 },
])

const totalValue = computed(() =>
  ingredients.value.reduce((sum, i) => sum + i.current_stock * i.cost_per_unit, 0)
)
const needsReorder = computed(() =>
  ingredients.value.filter(i => i.current_stock < i.reorder_level).length
)
const wellStocked = computed(() =>
  ingredients.value.filter(i => i.current_stock >= i.reorder_level).length
)
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-[#1A1A2E]">Baker's Dashboard</h1>
      <p class="text-gray-500 text-sm mt-1">Today's production overview</p>
    </div>

    <!-- Summary stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-8">
      <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Inventory Value</p>
        <p class="text-2xl font-bold text-[#1A1A2E] mt-1">KES {{ totalValue.toLocaleString() }}</p>
      </div>
      <div
        class="bg-white rounded-xl p-4 shadow-sm border"
        :class="needsReorder > 0 ? 'border-red-200 bg-red-50' : 'border-gray-100'"
      >
        <p class="text-xs font-medium uppercase tracking-wide"
           :class="needsReorder > 0 ? 'text-red-500' : 'text-gray-400'">
          Needs Reorder
        </p>
        <p class="text-2xl font-bold mt-1"
           :class="needsReorder > 0 ? 'text-red-600' : 'text-[#1A1A2E]'">
          {{ needsReorder }}
        </p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 col-span-2 sm:col-span-1">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Well Stocked</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ wellStocked }}</p>
      </div>
    </div>

    <!-- Ingredient stock -->
    <h2 class="text-lg font-semibold text-[#1A1A2E] mb-3">Ingredient Stock</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <StockIndicator
        v-for="ing in ingredients"
        :key="ing.id"
        :name="ing.name"
        :current="ing.current_stock"
        :reorder="ing.reorder_level"
        :unit="ing.unit"
        :cost-per-unit="ing.cost_per_unit"
      />
    </div>
  </div>
</template>

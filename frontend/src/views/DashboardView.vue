<script setup>
import { ref } from 'vue'
import StockIndicator from '../components/StockIndicator.vue'

// ---------------------------------------------------------------
// SAMPLE DATA: matches INGREDIENTS table from ERD
//
// In Week 6: GET /api/ingredients
// The API will return this exact shape from the database.
// cost_per_unit enables the stock VALUE calculation (KES per kg)
// which the admin needs for purchase order decisions.
// ---------------------------------------------------------------
const ingredients = ref([
  { id: 1, name: 'Wheat Flour', current_stock: 50, reorder_level: 20, unit: 'kg', cost_per_unit: 100 },
  { id: 2, name: 'Cinnamon', current_stock: 0.4, reorder_level: 0.5, unit: 'kg', cost_per_unit: 1200 },
  { id: 3, name: 'Eggs', current_stock: 120, reorder_level: 50, unit: 'pcs', cost_per_unit: 15 },
])

</script>

<template>
    <div>
    <h1 class="text-2xl font-bold">Baker's Dashboard</h1>
    <h2>Ingredient Stock</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

<style scoped>

</style>
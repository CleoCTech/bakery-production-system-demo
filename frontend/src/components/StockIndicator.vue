<script setup>
import { computed } from 'vue'

// ---------------------------------------------------------------
// PROPS: match INGREDIENTS table from the ERD
//
// In Week 6, the parent will get this data from:
//   GET /api/ingredients
// Response: [{ id, name, unit, current_stock, reorder_level, cost_per_unit }]
//

// We split the object into individual props here because this
// component doesn't need the full ingredient object — just the
// display-relevant fields. This is a design choice:
// - Individual props = clearer API, easier to validate
// - Object prop = simpler parent code, less verbose
// Both are valid. We choose individual props for learning.
// ---------------------------------------------------------------
const props = defineProps({
  name:    { type: String, required: true },        // INGREDIENTS.name - Wheat Flour
  current: { type: Number, required: true },        // INGREDIENTS.current_stock -50
  reorder: { type: Number, required: true },        // INGREDIENTS.reorder_level - 20
  unit:    { type: String, default: 'kg' },         // INGREDIENTS.unit - kg
  costPerUnit: { type: Number, default: 0 }         // INGREDIENTS.cost_per_unit -100
})

// ---------------------------------------------------------------
// COMPUTED: business logic for stock status
//
// These thresholds match the capstone business rules:
// - Below reorder level → red, dashboard alert
// - Reaches zero → production batches using it are blocked
// - The 2× reorder threshold (warning) is our addition for
//   early warning before it becomes critical.
// ---------------------------------------------------------------

const statusClass = computed(() => {
  if (props.current <= 0) return 'empty'
  if (props.current < props.reorder) return 'danger'
  if (props.current < props.reorder * 2) return 'warning'
  return 'healthy'
})
// statusClass ='healthy' // for testing the different states

const statusLabel = computed(() => {
  const labels = {
    empty: 'OUT OF STOCK',
    danger: 'Below reorder level',
    warning: 'Getting low',
    healthy: 'Good'
  }
  return labels[statusClass.value]
})
// statusLabel = 'Below reorder level' // for testing the different states
</script>

<template>

<div class="stock-indicator" :class="'stock-' + statusClass">
    <div class="stock-header">
      <div>
        <span class="ingredient-name">{{ name }}</span> --
        <span class="stock-status" :class="statusClass">{{ statusLabel }}</span>
      </div>
      <span class="stock-amount">{{ current }} {{ unit }}</span>
    </div>

    <!-- Visual bar -->
    <div class="bar-container">
      <div
        class="bar-fill"
        :class="statusClass"
        :style="{ width: barWidth + '%' }"
      ></div>
      <!-- Reorder level marker -->
      <div
        class="reorder-marker"
        :style="{ left: (props.reorder / (props.reorder * 3) * 100) + '%' }"
        title="Reorder level"
      ></div>
    </div>

    <!-- Details row -->
    <div class="stock-details">
      <span>Reorder at: {{ reorder }} {{ unit }}</span>
      <span v-if="costPerUnit > 0">Value: KES {{ stockValue }}</span>
    </div>

    <!-- Alert for low/empty stock -->
    <p v-if="current < reorder && current > 0" class="alert-text">
      ⚠ Order {{ (reorder - current).toFixed(1) }} {{ unit }} to reach reorder level
    </p>
    <p v-if="current <= 0" class="alert-text critical">
      🚫 Production blocked — no stock available
    </p>
  </div>
</template> 
<style scoped>
.stock-danger {
  background-color: #a91010;
}
.stick-warning {
  background-color: #e0a800;
}
.stock-healthy {
  background-color: #28a745;
}
.stock-empty {
  background-color: #6c757d;
}
</style>
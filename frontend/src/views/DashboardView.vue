<script setup>
import { ref, computed } from 'vue'
import StockIndicator from '../components/StockIndicator.vue'
import BatchCard from '../components/BatchCard.vue'

const ingredients = ref([
  { id: 1, name: 'Wheat Flour', current_stock: 50, reorder_level: 20, unit: 'kg', cost_per_unit: 100 },
  { id: 2, name: 'Cinnamon', current_stock: 0.4, reorder_level: 0.5, unit: 'kg', cost_per_unit: 1200 },
  { id: 3, name: 'Eggs', current_stock: 120, reorder_level: 50, unit: 'pcs', cost_per_unit: 15 },
])

// SAMPLE DATA: PRODUCTION_BATCHES

// GET /api/production-batches response includes product_name from JOIN with PRODUCTS:

const batches = ref([
  {
    id: 1,
    product_name: 'White Bread',
    product_id: 1,
    planned_quantity: 50,
    actual_quantity: 47,
    wastage_quantity: 3,
    status: 'done',
    started_at: '2026-05-20 06:30',
    completed_at: '2026-05-20 08:15'
  },
  {
    id: 2,
    product_name: 'Chocolate Cake',
    product_id: 2,
    planned_quantity: 30,
    actual_quantity: null,
    wastage_quantity: null,
    status: 'mixing',
    started_at: '2026-05-30 07:00',
    completed_at: null
  },
  {
    id: 3,
    product_name: 'Chocolate Cake',
    product_id: 2,
    planned_quantity: 20,
    actual_quantity: 18,
    wastage_quantity: 2,
    status: 'done',
    started_at: '2026-05-21 06:30',
    completed_at: '2026-05-21 09:00'
  },
  {
    id: 4,
    product_name: 'Cinnamon Roll',
    product_id: 5,
    planned_quantity: 30,
    actual_quantity: null,
    wastage_quantity: 0,
    status: 'planned',
    started_at: null,
    completed_at: null
  },
  {
    id: 5,
    product_name: 'White Bread',
    product_id: 1,
    planned_quantity: 50,
    actual_quantity: 35,
    wastage_quantity: 15,
    status: 'done',
    started_at: '2026-05-21 05:00',
    completed_at: '2026-05-21 07:00'
  },
  {
    id: 6,
    product_name: 'Meat Pie',
    product_id: 6,
    planned_quantity: 40,
    actual_quantity: null,
    wastage_quantity: 0,
    status: 'baking',
    started_at: '2026-05-21 10:00',
    completed_at: null
  },
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
// Batches actively being worked (not planned / done / failed)
const inProgress = computed(() =>
  batches.value.filter(b => ['mixing', 'baking', 'cooling'].includes(b.status)).length
)
// Completed batches that yielded under 80% of plan
const lowYield = computed(() =>
  batches.value.filter(b => {
    if (!b.actual_quantity || !b.planned_quantity) return false
    return (b.actual_quantity / b.planned_quantity) * 100 < 80
  }).length
)

// --- Completion panel state (Task 3) ---
const completingBatchId = ref(null)
const completionForm = ref({ actual: null, wastage: 0 })
const completingBatch = computed(() =>
  batches.value.find(b => b.id === completingBatchId.value) || null
)

function formatNow() {
  const now = new Date()
  const pad = n => String(n).padStart(2, '0')
  return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}`
}

function advanceBatch(batchId, newStatus) {
  const batch = batches.value.find(b => b.id === batchId)
  if (!batch) return

  // Marking a cooling batch done opens the completion panel instead of
  // finishing immediately — the baker records actual output + wastage there.
  if (newStatus === 'done') {
    completingBatchId.value = batchId
    completionForm.value = {
      actual: batch.actual_quantity ?? batch.planned_quantity,
      wastage: batch.wastage_quantity ?? 0
    }
    return
  }

  batch.status = newStatus
  // await axios.post(`/api/production-batches/${batchId}/advance`, { status: newStatus })
  // this triggers DB::transaction() - stock deduction - cost recording
}

function confirmCompletion() {
  const batch = completingBatch.value
  if (!batch) return
  batch.actual_quantity = completionForm.value.actual
  batch.wastage_quantity = completionForm.value.wastage
  batch.status = 'done'
  batch.completed_at = formatNow()
  completingBatchId.value = null
}

function cancelCompletion() {
  completingBatchId.value = null
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
      <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Well Stocked</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ wellStocked }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">In Progress</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ inProgress }}</p>
      </div>
      <div
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

    <!-- Production batches -->
    <h2 class="text-lg font-semibold text-[#1A1A2E] mt-8 mb-3">Production Batches</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <BatchCard
        v-for="batch in batches"
        :key="batch.id"
        :batch="batch"
        @advance-batch="advanceBatch"
      />
    </div>

    <!-- Completion panel (Task 3) -->
    <div v-if="completingBatch"
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
    </div>
  </div>
</template>

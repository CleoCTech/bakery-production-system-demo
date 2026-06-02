<script setup>
import { ref, computed } from 'vue'

// ---------------------------------------------------------------
// PROPS: match PRODUCTION_BATCHES table from ERD
//
// In Week 6: GET /api/production-batches
// Response includes product_name from a JOIN with PRODUCTS table:
//   SELECT pb.*, p.name as product_name
//   FROM production_batches pb
//   JOIN products p ON pb.product_id = p.id
//
// This is why product_name is in the data — it comes from the JOIN,
// not from the PRODUCTION_BATCHES table itself.
// ---------------------------------------------------------------
const props = defineProps({
  batch: { type: Object, required: true }
  // Expected shape:
  // {
  //   id: 1,                          ← PK
  //   product_name: 'White Bread',    ← from JOIN with PRODUCTS
  //   product_id: 1,                  ← FK to PRODUCTS
  //   planned_quantity: 50,           ← how many we plan to make
  //   actual_quantity: 47,            ← how many we actually made (null if not done)
  //   wastage_quantity: 3,            ← how many were lost
  //   status: 'done',                 ← planned|mixing|baking|cooling|done|failed
  //   started_at: '2026-05-20 06:30', ← when mixing started
  //   completed_at: '2026-05-20 08:15' ← when marked done
  // }
})

const emit = defineEmits(['advance-batch'])
// computaions for status label and colors
// Cost per unit = total batch ingredient cost / actual quantity
// (We can't calculate this yet — needs BATCH_COSTS data from API)
// ---------------------------------------------------------------
const yieldRate = computed(() => {
  if (!props.batch.actual_quantity || !props.batch.planned_quantity) return null
  return Math.round((props.batch.actual_quantity / props.batch.planned_quantity) * 100)
})

const yieldStatus = computed(() => {
  if (yieldRate.value === null) return null
  if (yieldRate.value >= 90) return { label: 'Excellent', class: 'bg-emerald-50 text-emerald-700' }
  if (yieldRate.value >= 80) return { label: 'Acceptable', class: 'bg-orange-50 text-orange-700' }
  return { label: 'Low yield — review needed', class: 'bg-red-50 text-red-700' }
})


// Duration calculation (if batch has start and end time)
const duration = computed(() => {
  if (!props.batch.started_at || !props.batch.completed_at) return null

  const start = new Date(props.batch.started_at)
  const end = new Date(props.batch.completed_at)
  const diffMins = Math.round((end - start) / 60000)
  const hours = Math.floor(diffMins / 60)
  const mins = diffMins % 60
  return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`
})

// Batch lifecycle: planned → mixing → baking → cooling → done
const nextStatus = computed(() => {
  const flow = {
    planned: { label: 'Start Mixing', next: 'mixing', icon: '🥣' },
    mixing:  { label: 'Move to Oven', next: 'baking', icon: '🔥' },
    baking:  { label: 'Move to Cooling', next: 'cooling', icon: '❄️' },
    cooling: { label: 'Mark Done', next: 'done', icon: '✅' },
  }
  return flow[props.batch.status] || null
})

// Status badge styling using Tailwind classes
const statusStyle = computed(() => {
  const config = {
    planned: { bg: 'bg-gray-100 text-gray-600', border: 'border-gray-400', icon: '📋' },
    mixing:  { bg: 'bg-amber-100 text-amber-700', border: 'border-amber-500', icon: '🥣' },
    baking:  { bg: 'bg-orange-100 text-orange-700', border: 'border-[#E8541E]', icon: '🔥' },
    cooling: { bg: 'bg-blue-100 text-blue-700', border: 'border-blue-500', icon: '❄️' },
    done:    { bg: 'bg-emerald-100 text-emerald-700', border: 'border-emerald-500', icon: '✅' },
    failed:  { bg: 'bg-red-100 text-red-700', border: 'border-red-500', icon: '❌' },
  }
  return config[props.batch.status] || config.planned
})

function handleAdvance() {
  if (nextStatus.value) {
    emit('advance-batch', props.batch.id, nextStatus.value.next)
  }
}

// Mark a mixing/baking batch as failed — reuses the advance event with the
// 'failed' status, which surfaces the red banner and hides the advance button.
function handleFail() {
  emit('advance-batch', props.batch.id, 'failed')
}
//data emission



</script>

<template>

<div
    class="bg-white rounded-xl p-5 shadow-sm border-l-4 flex flex-col gap-3"
    :class="[
      statusStyle.border,
      batch.status === 'failed' ? 'bg-red-50' : ''
    ]"
  >
    <!-- Header: product name + status badge -->
    <div class="flex justify-between items-center">
      <h4 class="text-base font-semibold text-[#1A1A2E]">{{ batch.product_name }}</h4>
      <span
        class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
        :class="statusStyle.bg"
      >
        {{ statusStyle.icon }} {{ batch.status }}
      </span>
    </div>

    <!-- Quantity details -->
    <div class="flex flex-col gap-1">
      <div class="flex justify-between text-sm">
        <span class="text-gray-400">Planned</span>
        <span class="font-medium text-gray-700">{{ batch.planned_quantity }} units</span>
      </div>

      <div v-if="batch.status === 'done' || batch.status === 'failed'" class="flex justify-between text-sm">
        <span class="text-gray-400">Actual output</span>
        <span class="font-medium text-gray-700">{{ batch.actual_quantity ?? 0 }} units</span>
      </div>

      <div v-if="batch.wastage_quantity > 0" class="flex justify-between text-sm">
        <span class="text-gray-400">Wastage</span>
        <span class="font-medium text-red-600">{{ batch.wastage_quantity }} units</span>
      </div>

      <div v-if="duration" class="flex justify-between text-sm">
        <span class="text-gray-400">Duration</span>
        <span class="font-medium text-gray-700">{{ duration }}</span>
      </div>
    </div>

    <!-- Yield rate (only for completed batches) -->
    <div
      v-if="yieldStatus"
      class="px-3 py-2 rounded-lg text-sm font-semibold"
      :class="yieldStatus.class"
    >
      Yield: {{ yieldRate }}% — {{ yieldStatus.label }}
    </div>

    <!-- Failed batch banner -->
    <div v-if="batch.status === 'failed'"
         class="bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium">
      Batch failed — full ingredient cost recorded as waste
    </div>

    <!-- Action button -->
    <button
      v-if="nextStatus"
      @click="handleAdvance"
      class="mt-auto w-full py-2.5 px-4 bg-[#1A1A2E] text-white rounded-lg
             text-sm font-medium hover:bg-[#E8541E] transition-colors"
    >
    Click to  {{ nextStatus.icon }} {{ nextStatus.label }}
    </button>

    <!-- Mark failed (only while mixing or baking) -->
    <button
      v-if="batch.status === 'mixing' || batch.status === 'baking'"
      @click="handleFail"
      class="self-start text-xs font-medium text-red-500 hover:text-red-700 hover:underline transition-colors"
    >
      ❌ Mark Failed
    </button>
  </div>
</template>
<script setup>
import { computed } from 'vue'

const props = defineProps({
  name:        { type: String, required: true },
  current:     { type: Number, required: true },
  reorder:     { type: Number, required: true },
  unit:        { type: String, default: 'kg' },
  costPerUnit: { type: Number, default: 0 }
})

const statusClass = computed(() => {
  if (props.current <= 0) return 'empty'
  if (props.current < props.reorder) return 'danger'
  if (props.current < props.reorder * 2) return 'warning'
  return 'healthy'
})

const statusLabel = computed(() => {
  const labels = { empty: 'OUT OF STOCK', danger: 'Below reorder', warning: 'Getting low', healthy: 'Good' }
  return labels[statusClass.value]
})

const statusTextColor = computed(() => {
  const colors = { empty: 'text-red-600', danger: 'text-red-600', warning: 'text-amber-600', healthy: 'text-emerald-600' }
  return colors[statusClass.value]
})

const barColor = computed(() => {
  const colors = { empty: 'bg-red-500', danger: 'bg-red-500', warning: 'bg-amber-500', healthy: 'bg-emerald-500' }
  return colors[statusClass.value]
})

const borderColor = computed(() => {
  const colors = { empty: 'border-red-500', danger: 'border-red-500', warning: 'border-amber-500', healthy: 'border-emerald-500' }
  return colors[statusClass.value]
})

const barWidth = computed(() => {
  const max = props.reorder * 3
  return Math.min((props.current / max) * 100, 100)
})

const stockValue = computed(() => {
  return (props.current * props.costPerUnit).toLocaleString()
})
</script>

<template>
  <div
    class="bg-white rounded-xl p-4 shadow-sm border-l-4 transition-colors"
    :class="[borderColor, statusClass === 'empty' ? 'bg-red-50' : '']"
  >
    <!-- Header -->
    <div class="flex justify-between items-start mb-2">
      <div>
        <span class="font-semibold text-[#1A1A2E] text-sm block">{{ name }}</span>
        <span class="text-[0.65rem] font-semibold uppercase tracking-wide" :class="statusTextColor">
          {{ statusLabel }}
        </span>
      </div>
      <span class="text-lg font-bold text-[#1A1A2E]">{{ current }} {{ unit }}</span>
    </div>

    <!-- Bar -->
    <div class="w-full h-2 bg-gray-100 rounded-full overflow-visible relative my-2">
      <div
        class="h-full rounded-full transition-all duration-400"
        :class="barColor"
        :style="{ width: (statusClass === 'empty' ? 2 : barWidth) + '%' }"
      ></div>
      <!-- Reorder level marker -->
      <div
        class="absolute top-[-3px] w-0.5 h-3.5 bg-gray-600 rounded-sm"
        :style="{ left: (reorder / (reorder * 3) * 100) + '%' }"
        :title="'Reorder at ' + reorder + ' ' + unit"
      ></div>
    </div>

    <!-- Details -->
    <div class="flex justify-between text-xs text-gray-400 mt-1">
      <span>Reorder at: {{ reorder }} {{ unit }}</span>
      <span v-if="costPerUnit > 0">Value: KES {{ stockValue }}</span>
    </div>

    <!-- Alerts -->
    <p v-if="current < reorder && current > 0"
       class="text-red-600 text-xs font-medium mt-2 bg-red-50 px-2 py-1 rounded">
      ⚠ Order {{ (reorder - current).toFixed(1) }} {{ unit }} to reach reorder level
    </p>
    <p v-if="current <= 0"
       class="text-white text-xs font-medium mt-2 bg-red-600 px-2 py-1 rounded">
      🚫 Production blocked — no stock available
    </p>
  </div>
</template>
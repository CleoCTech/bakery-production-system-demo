// ---------------------------------------------------------------
// COMPOSABLE: batch yield calculation
//
// Used by: BatchCard, DashboardView stats, ReportsView
// Implements capstone business rule:
//   "Yield rate = (actual / planned) × 100. Below 80% triggers
//    a low-yield flag for admin review."
// ---------------------------------------------------------------

import { computed } from 'vue'

export function useYieldCalculation(plannedQty, actualQty) {

    const yieldRate = computed(() => {
        if (!actual.value || !planned.value || planned.value === 0) return null
        return Math.round((actual.value / planned.value) * 100)
    })

    const yieldStatus = computed(() => {
        if (yieldRate.value === null) return null
        if (yieldRate.value >= 90) return { label: 'Excellent', class: 'bg-emerald-50 text-emerald-700' }
        if (yieldRate.value >= 80) return { label: 'Acceptable', class: 'bg-orange-50 text-orange-700' }
        return { label: 'Low yield — review needed', class: 'bg-red-50 text-red-700' }
    })

    const isLowYield = computed(() =>
        yieldRate.value !== null && yieldRate.value < 80
    )

    return { yieldRate, isLowYield, yieldStatus }        


}
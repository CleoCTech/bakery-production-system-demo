// ---------------------------------------------------------------
// COMPOSABLE: reusable stock status logic
//
// Used by: StockIndicator, BatchPlanningForm, PurchaseOrderView
// This is the SAME logic that was in StockIndicator.vue,
// extracted so any component can use it.
//
// Usage:
//   const { status, statusColor, needsReorder, barWidth } = useStockStatus(
//     toRef(props, 'current'),
//     toRef(props, 'reorder')
//   )
// ---------------------------------------------------------------

import { computed } from 'vue'

// eggs current stock: 120
// eggs reorder level: 50 
export function useStockStatus(currentStock, reorderLevel) {

    const status = computed(() => {
        if (currentStock.value <= 0) return 'empty'
        if (currentStock.value < reorderLevel.value) return 'danger'
        if (currentStock.value < reorderLevel.value * 2) return 'warning'
        return 'healthy'
    });

    const statusLabel = computed(() => {
        const labels = {
            empty: 'OUT OF STOCK',
            danger: 'Below reorder',
            warning: 'Getting low',
            healthy: 'Good'
        }
        return labels[status.value]
    })

    const statusColor = computed(() => {
        const colors = {
            empty: 'text-red-600',
            danger: 'text-red-600',
            warning: 'text-amber-600',
            healthy: 'text-emerald-600'
        }
        return colors[status.value]
    })

    const barColor = computed(() => {
        const colors = {
            empty: 'bg-red-500',
            danger: 'bg-red-500',
            warning: 'bg-amber-500',
            healthy: 'bg-emerald-500'
        }
        return colors[status.value]
    })

    const borderColor = computed(() => {
        const colors = {
            empty: 'border-red-500',
            danger: 'border-red-500',
            warning: 'border-amber-500',
            healthy: 'border-emerald-500'
        }
        return colors[status.value]
    })

    const needsReorder = computed(() =>
        currentStock.value < reorderLevel.value
    )

    const barWidth = computed(() => {
        const max = reorderLevel.value * 3
        return Math.min((currentStock.value / max) * 100, 100)
    })


    return { status, statusLabel, statusColor, barColor, borderColor, needsReorder, barWidth }

}
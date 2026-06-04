import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

import { useIngredientStore } from './ingredientStore'

export const useBatchStore = defineStore('batch', () => {

    //opening(instantiation) and then later assemble of imported car 
    const ingredientStore = useIngredientStore()

    // STATE: matches PRODUCTION_BATCHES table from ERD
    const batches = ref([
        {
        id: 1, product_name: 'White Bread', product_id: 1,
        planned_quantity: 50, actual_quantity: 47, wastage_quantity: 3,
        status: 'done', started_at: '2026-05-20T06:00:00', completed_at: '2026-05-20T08:30:00'
        },
        {
        id: 2, product_name: 'Mandazi', product_id: 4,
        planned_quantity: 100, actual_quantity: null, wastage_quantity: 0,
        status: 'mixing', started_at: '2026-05-20T09:00:00', completed_at: null
        },
        {
        id: 3, product_name: 'Cinnamon Roll', product_id: 3,
        planned_quantity: 30, actual_quantity: null, wastage_quantity: 0,
        status: 'planned', started_at: null, completed_at: null
        },
    ])

      // GETTERS
    const totalBatches = computed(() => batches.value.length)
    const doneBatches = computed(() => batches.value.filter(b => b.status === 'done').length)
    const inProgressBatches = computed(() =>
        batches.value.filter(b => ['mixing', 'baking', 'cooling'].includes(b.status)).length
    )

    function advanceBatch(batchId) {
        const batch = batches.value.find(b => b.id === batchId)
        if (!batch) return

        // If entering 'mixing', deduct stock (simplified)
        if (newStatus === 'mixing') {
            const ingredientStore = useIngredientStore()
            try {
                // Simplified: deduct 0.5kg flour per unit for White Bread
                // In reality, this reads from RECIPE_ITEMS (the BOM)
                ingredientStore.deductStock(1, batch.planned_quantity * 0.5) // flour
                batch.started_at = new Date().toISOString()
            } catch (err) {
                alert(err.message)
                return // Don't advance if stock insufficient
            }
        }

        batch.status = newStatus

        if (newStatus === 'done') {
            batch.completed_at = new Date().toISOString()
        }
    }

    return {
        batches, totalBatches, doneBatches, inProgressBatches,
        advanceBatch,
    }

})    

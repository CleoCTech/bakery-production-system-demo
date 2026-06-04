import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

//export the car
export const useIngredientStore = defineStore('ingredient', () => {

    // ==================== STATE ====================
    // Same data shape as INGREDIENTS table from the ERD
    // fetchIngredients() will replace this hardcoded data in Week 6
    const ingredients = ref([
        { id: 1, name: 'Wheat Flour', current_stock: 50, reorder_level: 20, unit: 'kg', cost_per_unit: 100 },
        { id: 2, name: 'Yeast', current_stock: 2, reorder_level: 1, unit: 'kg', cost_per_unit: 800 },
        { id: 3, name: 'Sugar', current_stock: 30, reorder_level: 10, unit: 'kg', cost_per_unit: 150 },
        { id: 4, name: 'Cocoa Powder', current_stock: 5, reorder_level: 3, unit: 'kg', cost_per_unit: 600 },
        { id: 5, name: 'Butter', current_stock: 8, reorder_level: 5, unit: 'kg', cost_per_unit: 500 },
        { id: 6, name: 'Eggs', current_stock: 120, reorder_level: 50, unit: 'pcs', cost_per_unit: 15 },
        { id: 7, name: 'Milk', current_stock: 20, reorder_level: 10, unit: 'ltr', cost_per_unit: 70 },
        { id: 8, name: 'Cinnamon', current_stock: 0.4, reorder_level: 0.5, unit: 'kg', cost_per_unit: 1200 },
    ])

    const isLoading = ref(false)
    const error = ref(null)

    // ==================== GETTERS ====================
    const lowStockItems = computed(() =>
        ingredients.value.filter(i => i.current_stock < i.reorder_level)
    )

    const lowStockCount = computed(() => lowStockItems.value.length)

    const totalStockValue = computed(() =>
        ingredients.value.reduce((sum, i) => sum + (i.current_stock * i.cost_per_unit), 0)
    )

    // reorder level
    // //true/false: is any ingredient below reorder level - rerun?

    // const needsReorder = computed(() => lowStockItems.value.length > 0)

    function deductStock(ingredientId, qty) {
        const ingredient = ingredients.value.find(i => i.id === ingredientId)
        if (ingredient) {
            if (ingredient.current_stock < qty) {
                throw new Error(`Insufficient stock: ${ingredient.name} has ${ingredient.current_stock}${ingredient.unit}, needs ${qty}`)
            }
            ingredient.current_stock -= qty
            // Week 6: this happens inside DB::transaction() on the server
            // The server also creates a STOCK_MOVEMENTS record
        }
    }

    function addStock(ingredientId, qty) {
        const ingredient = ingredients.value.find(i => i.id === ingredientId)
        if (ingredient) {
        ingredient.current_stock += qty
        }
    }

    return { ingredients, isLoading, error, lowStockItems, lowStockCount, totalStockValue, deductStock, addStock }
})
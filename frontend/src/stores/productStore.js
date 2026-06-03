// ---------------------------------------------------------------
// PINIA STORE: Single source of truth for all product data
//
// This store is used by:
// - ProductsView (catalog page — browse, filter, search)
// - SalesView (sales page — select product to sell)
// - DashboardView (dashboard — product count stat)
//
// In Week 6, the hardcoded array becomes an API call:
//   await axios.get('/api/products')
//
// The store pattern: STATE (data) + GETTERS (computed) + ACTIONS (functions)
// ---------------------------------------------------------------


import {defineStore} from 'pinia'
import {ref, computed} from 'vue'

export const useProductStore = defineStore('product', () => {

    // ==================== STATE ====================
    // Same data shape as PRODUCTS table from the ERD
    const products = ref([
        { id: 1, name: 'White Bread', category: 'bread', selling_price: 60, shelf_life_hours: 24, unit: 'loaf', is_active: true },
        { id: 2, name: 'Chocolate Cake', category: 'cake', selling_price: 350, shelf_life_hours: 72, unit: 'piece', is_active: true },
        { id: 3, name: 'Cinnamon Roll', category: 'pastry', selling_price: 40, shelf_life_hours: 12, unit: 'piece', is_active: true },
        { id: 4, name: 'Mandazi', category: 'bun', selling_price: 10, shelf_life_hours: 12, unit: 'piece', is_active: true },
        { id: 5, name: 'Brown Bread', category: 'bread', selling_price: 65, shelf_life_hours: 24, unit: 'loaf', is_active: true },
        { id: 6, name: 'Meat Pie', category: 'pastry', selling_price: 80, shelf_life_hours: 8, unit: 'piece', is_active: true },
        { id: 7, name: 'Chapati', category: 'bread', selling_price: 20, shelf_life_hours: 8, unit: 'piece', is_active: true },
    ])

    const isLoading = ref(false)
    const error = ref(null)

    // ==================== GETTERS ====================
    const productCount = computed(() => products.value.length)

    const activeProducts = computed(() => products.value.filter(p => p.is_active))

    const categories = computed(() => {
        const cats = [...new Set(products.value.map(p => p.category))]
        return cats.sort()
    })

    const totalCatalogValue = computed(() => {
        return products.value.reduce((sum,p) => sum + p.selling_price, 0)
    })


    // ==================== ACTIONS ====================
    function addProduct(newProduct) {
        // In a real app, this would POST to the API and get back the created product with an ID
        const newId = Math.max(...products.value.map(p => p.id)) + 1
        products.value.push({...newProduct, id: newId})
    }

    function updateProduct(productId, updates) {
        // Week 6: await axios.put(`/api/products/${productId}`, updates)
        const product = products.value.find(p => p.id === productId)
        if (product) Object.assign(product, updates)
    }

    function toggleActive(productId) {
        const product = products.value.find(p => p.id === productId)
        if (product) product.is_active = !product.is_active
    }

    function deleteProduct(productId) {
        // Week 6: await axios.delete(`/api/products/${productId}`)
        products.value = products.value.filter(p => p.id !== productId)
    }

    // In Week 6, this replaces the hardcoded data:
    // async function fetchProducts() {
    //   isLoading.value = true
    //   error.value = null
    //   try {
    //     const response = await axios.get('/api/products')
    //     products.value = response.data
    //   } catch (err) {
    //     error.value = 'Failed to load products'
    //   } finally {
    //     isLoading.value = false
    //   }
    // }

    return {
        // State
        products, isLoading, error,
        // Getters
        productCount, activeProducts, categories, totalCatalogValue,
        // Actions
        addProduct, updateProduct, toggleActive,deleteProduct
    }
})

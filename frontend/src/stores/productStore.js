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
import api from '../api/axios'  // Axios instance with baseURL and auth headers


export const useProductStore = defineStore('product', () => {

    // STATE
  const products = ref([])     // ← was hardcoded, now starts EMPTY
  const isLoading = ref(false)
  const error = ref(null)

  // GETTERS (unchanged — they work on whatever data is in products)
  const productCount = computed(() => products.value.length)

  const activeProducts = computed(() => products.value.filter(p => p.is_active))

  const categories = computed(() => {
    const cats = [...new Set(products.value.map(p => p.category?.name || 'uncategorized'))]
    return cats.sort()
  })

  // ==================== ACTIONS — NOW CALL THE API ====================

  // FETCH all products from database
  async function fetchProducts() {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/products')
      products.value = response.data
    } catch (err) {
      error.value = 'Failed to load products'
      console.error('fetchProducts error:', err)
    } finally {
      isLoading.value = false
    }
  }

  // CREATE a new product
  async function addProduct(productData) {
    try {
      const response = await api.post('/products', productData)
      products.value.push(response.data)  // Add server response (has id, timestamps)
      return response.data
    } catch (err) {
      // If validation fails, Laravel returns 422 with error details
      if (err.response?.status === 422) {
        throw err.response.data  // Pass validation errors to the component
      }
      throw err
    }
  }

  // UPDATE a product
  async function updateProduct(productId, updates) {
    try {
      const response = await api.put(`/products/${productId}`, updates)
      const index = products.value.findIndex(p => p.id === productId)
      if (index !== -1) products.value[index] = response.data
      return response.data
    } catch (err) {
      if (err.response?.status === 422) throw err.response.data
      throw err
    }
  }

  // DELETE a product
  async function deleteProduct(productId) {
    try {
      await api.delete(`/products/${productId}`)
      products.value = products.value.filter(p => p.id !== productId)
    } catch (err) {
      console.error('deleteProduct error:', err)
      throw err
    }
  }

    return {
        // State
        products, isLoading, error,
        // Getters
        productCount, activeProducts, categories,
        // Actions
        addProduct, updateProduct,deleteProduct, fetchProducts
    }
})

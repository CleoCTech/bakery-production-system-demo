import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../api/axios'

export const useBatchStore = defineStore('batches', () => {
  const batches = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  const totalBatches = computed(() => batches.value.length)
  const doneBatches = computed(() => batches.value.filter(b => b.status === 'done').length)
  const inProgressBatches = computed(() =>
    batches.value.filter(b => ['mixing', 'baking', 'cooling'].includes(b.status)).length
  )

  async function fetchBatches() {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/batches')
      batches.value = response.data
    } catch (err) {
      error.value = 'Failed to load batches'
    } finally {
      isLoading.value = false
    }
  }

  async function advanceBatch(batchId) {
    try {
      const response = await api.patch(`/batches/${batchId}/advance`)
      // Replace the batch in the array with the updated one
      const index = batches.value.findIndex(b => b.id === batchId)
      if (index !== -1) batches.value[index] = response.data
      return response.data
    } catch (err) {
      // Show the server error message (e.g., "Insufficient Wheat Flour")
      const message = err.response?.data?.message || 'Failed to advance batch'
      alert(message)
      throw err
    }
  }

  async function createBatch(productId, plannedQuantity) {
    try {
      const response = await api.post('/batches', {
        product_id: productId,
        planned_quantity: plannedQuantity,
      })
      batches.value.push(response.data)
      return response.data
    } catch (err) {
      throw err
    }
  }

  return {
    batches, isLoading, error,
    totalBatches, doneBatches, inProgressBatches,
    fetchBatches, advanceBatch, createBatch,
  }
})
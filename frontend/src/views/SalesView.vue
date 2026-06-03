<script setup>
import { ref, computed } from 'vue'
import ProductCard from '../components/ProductCard.vue'
// SAMPLE DATA: Products - this would normally come from an API call to /api/products

const products = ref([
  { id: 1, name: 'White Bread', selling_price: 50 },
  { id: 2, name: 'Chocolate Cake', selling_price: 200 },
  { id: 3, name: 'Croissant', selling_price: 30 },
])

// Form State for recording a new sale

const selectedProductId = ref(null)
const quantity = ref(1) 
const paymentMethod = ref('cash')
const mpesaRef = ref('')

// Sales history - this would normally come from an API call to /api/sales=today or similar endpoint
const sales = ref([])
// COMPUTED: find the selected product details based on selectedProductId

const selectedProduct = computed(() => {
  return products.value.find(p => p.id === selectedProductId.value) || null
})

// COMPUTED: calculate total price based on selected product and quantity
const totalAmount = computed(() => { 
  if (!selectedProduct.value) return 0
  return selectedProduct.value.selling_price * quantity.value
})

// Computed: today's summary stats
const todayRevenue = computed(() => {
  return sales.value.reduce((sum, sale) => sum + sale.total_amount, 0)
})

const salesCount = computed(() => sales.value.length)

function recordSale() {
  if (!selectedProduct.value) {
    alert('Please select a product')
    return
  }
  // In a real app, you would send this data to the backend API to create a new sale record
  const newSale = {
    id: sales.value.length + 1,
    product_id: selectedProduct.value.id,
    product_name: selectedProduct.value.name,
    quantity: quantity.value,
    total_amount: totalAmount.value,
    payment_method: paymentMethod.value,
    mpesa_ref: paymentMethod.value === 'mpesa' ? mpesaRef.value : null,
    timestamp: new Date().toISOString()
  }
  sales.value.push(newSale)
  
  // Reset form
  selectedProductId.value = null
  quantity.value = 1
  paymentMethod.value = 'cash'
  mpesaRef.value = ''
}

</script>

<template>
<div>
    <h1 class="text-2xl font-bold text-[#1A1A2E] mb-1">Record a Sale</h1>
    <p class="text-gray-500 text-sm mb-6">Cashier sales interface</p>

    <!-- Summary stats -->
    <div class="flex gap-4 mb-6">
      <div class="bg-white rounded-xl px-6 py-4 shadow-sm text-center min-w-[150px]">
        <span class="block text-2xl font-bold text-[#1A1A2E]">{{ salesCount }}</span>
        <span class="text-xs text-gray-500">Sales today</span>
      </div>
      <div class="bg-white rounded-xl px-6 py-4 shadow-sm text-center min-w-[150px]">
        <span class="block text-2xl font-bold text-[#E8541E]">KES {{ todayRevenue.toLocaleString() }}</span>
        <span class="text-xs text-gray-500">Revenue today</span>
      </div>
    </div>

    <!-- Two-column layout: form + history -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

      <!-- Sale form -->
      <div class="bg-white rounded-xl p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1A1A2E] mb-4">New Sale</h2>

        <label class="block text-sm font-medium text-gray-600 mb-1">Product</label>
        <select
          v-model.number="selectedProductId"
          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm
                 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 mb-4"
        >
          <option :value="null" disabled>Choose a product</option>
          <option v-for="p in products" :key="p.id" :value="p.id">
            {{ p.name }} — KES {{ p.selling_price }}
          </option>
        </select>

        <label class="block text-sm font-medium text-gray-600 mb-1">Quantity</label>
        <input
          v-model.number="quantity"
          type="number"
          min="1"
          max="100"
          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm
                 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 mb-4"
        >

        <label class="block text-sm font-medium text-gray-600 mb-2">Payment method</label>
        <div class="flex gap-3 mb-4">
          <label
            class="flex items-center gap-2 px-4 py-2.5 rounded-lg cursor-pointer text-sm transition-colors"
            :class="paymentMethod === 'cash'
              ? 'bg-emerald-100 text-emerald-800 ring-2 ring-emerald-300'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
          >
            <input type="radio" v-model="paymentMethod" value="cash" class="hidden">
            💵 Cash
          </label>
          <label
            class="flex items-center gap-2 px-4 py-2.5 rounded-lg cursor-pointer text-sm transition-colors"
            :class="paymentMethod === 'mpesa'
              ? 'bg-blue-100 text-blue-800 ring-2 ring-blue-300'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
          >
            <input type="radio" v-model="paymentMethod" value="mpesa" class="hidden">
            📱 M-Pesa
          </label>
        </div>

        <!-- Conditional M-Pesa reference field -->
        <div v-if="paymentMethod === 'mpesa'" class="mb-4 animate-fade-in">
          <label class="block text-sm font-medium text-gray-600 mb-1">M-Pesa Reference</label>
          <input
            v-model="mpesaRef"
            type="text"
            placeholder="e.g., SHK7X9M2LP"
            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm
                   focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          >
        </div>

        <!-- Live total -->
        <div v-if="selectedProduct"
             class="flex justify-between items-center bg-orange-50 px-4 py-3 rounded-lg
                    border-l-4 border-[#E8541E] mb-4">
          <span class="text-sm text-gray-600">Total</span>
          <span class="text-xl font-bold text-[#E8541E]">KES {{ totalAmount.toLocaleString() }}</span>
        </div>

        <button
          @click="recordSale"
          :disabled="!selectedProduct"
          class="w-full py-3 bg-[#1A1A2E] text-white rounded-lg font-medium
                 hover:bg-[#E8541E] transition-colors
                 disabled:bg-gray-300 disabled:cursor-not-allowed"
        >
          Record Sale
        </button>
      </div>

      <!-- STUDENTS: Build the sales history panel here (Task 1) -->
      <div class="bg-white rounded-xl p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1A1A2E] mb-4">Today's Sales</h2>
        <p class="text-gray-400 text-sm">Sales history will appear here. Complete Task 1 to build this.</p>
      </div>

    </div>
  </div>
</template>
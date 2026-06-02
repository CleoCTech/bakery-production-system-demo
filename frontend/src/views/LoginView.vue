<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'


const router = useRouter()

const email = ref('')
const password = ref('')
const role = ref('baker')
const isLoading = ref(false)
const errorMessage = ref('')

async function handleLogin() {
  errorMessage.value = ''
  isLoading.value = true

  // Simulate API delay
  await new Promise(resolve => setTimeout(resolve, 800))

  // In Week 6:
  // try {
  //   const response = await axios.post('/api/login', { email, password })
  //   authStore.setUser(response.data.user, response.data.token)
  //   router.push('/')
  // } catch (err) {
  //   errorMessage.value = 'Invalid email or password'
  // }

  console.log(`Login: ${email.value} as ${role.value}`)
  isLoading.value = false
  router.push('/')
}


</script>

<template>
  <div class="flex justify-center items-center min-h-[80vh] px-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-lg p-8">

      <!-- Branding -->
      <div class="text-center mb-8">
        <span class="text-5xl block mb-2">🍞</span>
        <h1 class="text-xl font-bold text-[#1A1A2E]">Bakery Production System</h1>
        <p class="text-gray-400 text-sm mt-1">Sign in to your account</p>
      </div>

      <!-- Error banner -->
      <div v-if="errorMessage"
           class="bg-red-50 text-red-700 border-l-4 border-red-500 px-4 py-3 rounded-lg text-sm mb-4">
        {{ errorMessage }}
      </div>

      <!-- Email -->
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
        <input
          v-model="email"
          type="email"
          placeholder="baker@bakery.co.ke"
          :disabled="isLoading"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100
                 disabled:bg-gray-50 disabled:text-gray-400 transition"
        >
      </div>

      <!-- Password -->
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
        <input
          v-model="password"
          type="password"
          placeholder="Enter your password"
          :disabled="isLoading"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100
                 disabled:bg-gray-50 disabled:text-gray-400 transition"
        >
      </div>

      <!-- Role selector -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Login as</label>
        <select
          v-model="role"
          :disabled="isLoading"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white
                 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100
                 disabled:bg-gray-50 disabled:text-gray-400 transition"
        >
          <option value="admin">👔 Admin (Owner/Manager)</option>
          <option value="baker">👨‍🍳 Baker (Production)</option>
          <option value="cashier">💰 Cashier (Sales)</option>
        </select>
      </div>

      <!-- Submit -->
      <button
        @click="handleLogin"
        :disabled="isLoading"
        class="w-full py-3 bg-[#1A1A2E] text-white rounded-lg font-semibold text-sm
               hover:bg-[#E8541E] transition-colors
               disabled:bg-gray-400 disabled:cursor-not-allowed"
      >
        {{ isLoading ? 'Signing in...' : 'Sign In' }}
      </button>

      <!-- STUDENTS: Add validation errors and extra features here -->
    </div>
  </div>
</template>
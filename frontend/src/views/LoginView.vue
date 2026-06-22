<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const touched = ref({ email: false, password: false })

const emailError = computed(() => {
  if (!touched.value.email) return ''
  if (!email.value) return 'Email is required'
  if (!email.value.includes('@')) return 'Enter a valid email'
  return ''
})

const passwordError = computed(() => {
  if (!touched.value.password) return ''
  if (!password.value) return 'Password is required'
  if (password.value.length < 6) return 'Minimum 6 characters'
  return ''
})

const isFormValid = computed(() =>
  email.value && password.value.length >= 6 && !emailError.value && !passwordError.value
)


watch([email, password], () => {
  authStore.error = null
})


async function handleLogin() {
  touched.value = { email: true, password: true }
  if (!isFormValid.value) return

  try {
    await authStore.login(email.value, password.value)
    // authStore.login handles navigation
  } catch (err) {
    // Error already set in authStore
  }
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


      <!-- Error banner — now from the auth store -->
    <div v-if="authStore.error"
        class="bg-red-50 text-red-700 border-l-4 border-red-500 px-4 py-3 rounded-lg text-sm mb-4">
      {{ authStore.error }}
    </div>

      <!-- Email -->
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
        <input
          v-model="email"
          type="email"
          placeholder="baker@bakery.co.ke"
          :disabled="authStore.isLoading"
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
          :disabled="authStore.isLoading"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100
                 disabled:bg-gray-50 disabled:text-gray-400 transition"
        >
      </div>


      <!-- Submit button — uses store loading state -->
      <button
        @click="handleLogin"
        :disabled="authStore.isLoading || !isFormValid"
        class="w-full py-3 bg-[#1A1A2E] text-white rounded-lg font-semibold text-sm
              hover:bg-[#E8541E] transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed cursor-pointer"
      >
        {{ authStore.isLoading ? 'Signing in...' : 'Sign In' }}
      </button>

      <!-- STUDENTS: Add validation errors and extra features here -->
    </div>
  </div>
</template>
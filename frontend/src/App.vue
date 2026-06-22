<script setup>
import { useAuthStore } from './stores/authStore'

const authStore = useAuthStore()
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-[#1A1A2E] text-white px-8 py-4 flex justify-between items-center">
      <span class="text-xl font-bold">🍞 Bakery Production System</span>

      <div class="flex items-center gap-6">
        <!-- Show nav links only when logged in -->
        <template v-if="authStore.isLoggedIn">
          <!-- All roles see dashboard -->
          <router-link to="/" class="text-gray-400 hover:text-[#E8541E] transition-colors">
            Dashboard
          </router-link>

          <!-- Admin and baker see products -->
          <router-link v-if="authStore.isAdmin || authStore.isBaker"
            to="/products" class="text-gray-400 hover:text-[#E8541E] transition-colors">
            Products
          </router-link>

          <!-- Admin and cashier see sales -->
          <router-link v-if="authStore.isAdmin || authStore.isCashier"
            to="/sales" class="text-gray-400 hover:text-[#E8541E] transition-colors">
            Sales
          </router-link>

          <!-- User info + logout -->
          <span class="text-sm text-gray-400">
            {{ authStore.userName }} ({{ authStore.userRole }})
          </span>
          <button @click="authStore.logout()"
            class="text-sm text-gray-400 hover:text-red-400 transition-colors cursor-pointer">
            Logout
          </button>
        </template>

        <!-- Show login link when NOT logged in -->
        <router-link v-else to="/login"
          class="text-gray-400 hover:text-[#E8541E] transition-colors cursor-pointer">
          Login
        </router-link>
      </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">
      <router-view />
    </main>
  </div>
</template>

<style>
.router-link-active {
  color: #E8541E !important;
  font-weight: 600;
}
</style>
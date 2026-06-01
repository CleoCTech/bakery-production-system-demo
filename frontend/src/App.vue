<script setup>
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation bar -->
    <nav class="bg-[#1A1A2E] text-white px-4 sm:px-8 py-4 flex flex-col sm:flex-row gap-3 sm:gap-0 sm:justify-between sm:items-center">
      <span class="text-lg sm:text-xl font-bold">🍞 Bakery Production System</span>
      <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm sm:text-base">
        <router-link to="/" class="text-gray-400 hover:text-[#E8541E] transition-colors">Dashboard</router-link>
        <router-link to="/products" class="text-gray-400 hover:text-[#E8541E] transition-colors">Products</router-link>
        <router-link to="/sales" class="text-gray-400 hover:text-[#E8541E] transition-colors">Sales</router-link>
        <router-link to="/login" class="text-gray-400 hover:text-[#E8541E] transition-colors">Login</router-link>
      </div>
    </nav>

    <!-- Page content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
      <router-view />
    </main>
  </div>
</template>

<style>
/* Active router link styling — needs global CSS since Tailwind can't target dynamic classes easily */
.router-link-active {
  color: #E8541E !important;
  font-weight: 600;
}
</style>
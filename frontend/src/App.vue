<script setup>
import { ref } from 'vue'


</script>

<template>
<div class="app">
  <nav class="navbar">
        <div class="brand"> Bakery Production System</div>
        <div class="nav-links">
          <router-link to="/">Dashboard</router-link>
          <router-link to="/products">Products</router-link>
          <router-link to="/sales">Sales</router-link>
          <router-link to="/login">Login</router-link>
        </div>
  </nav>
  <!-- Page content renders here -->
  <main class="content">
    <router-view />
  </main>

</div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f5f5f5;
  color: #333;
}

.navbar {
  background-color: #1A1A2E;
  color: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand {
  font-size: 1.3rem;
  font-weight: bold;
}

.nav-links {
  display: flex;
  gap: 1.5rem;
}

.nav-links a {
  color: #aaa;
  text-decoration: none;
  font-size: 0.95rem;
}

.nav-links a.router-link-active {
  color: #E8541E;
  font-weight: bold;
}

.content {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}
</style>

<script setup>
import { ref } from 'vue'

const products =  ref([
  { id: 1, name: 'Bread', prices: 60, category: 'Bakery', shelf_life: 24 },
  { id: 2, name: 'Cake', prices: 150, category: 'Bakery', shelf_life: 48 },
  { id: 3, name: 'Pastry', prices: 100, category: 'Bakery', shelf_life: 36 },
  { id: 4, name: 'Muffin', prices: 80, category: 'Bakery', shelf_life: 24 }
])
</script>

<template>
<h1>Bakery Production System</h1>

<div v-for="product in products" :key="product.id">
  <h2>{{ product.name }}</h2>
  <p>Price: {{ product.prices }}</p>
  <p>Category: {{ product.category }}</p>
  <p>Shelf Life: {{ product.shelf_life }} hours</p>

</div>
</template>

<style scoped>

</style>

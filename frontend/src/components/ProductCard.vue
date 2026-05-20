<script setup>
import { computed } from 'vue'

// ---------------------------------------------------------------
// PROPS: data this component receives from its parent
// Field names match our PRODUCTS table from the ERD exactly.
// When we connect to the Laravel API in Week 6, the JSON response
// will have these same field names — no renaming needed.
// ---------------------------------------------------------------
const props = defineProps({
  product: {
    type: Object,
    required: true
    // Expected shape (from PRODUCTS table in ERD):
    // {
    //   id: 1,                    ← PRODUCTS.id (PK, auto-increment)
    //   name: 'White Bread',      ← PRODUCTS.name (VARCHAR 100)
    //   category: 'bread',        ← PRODUCTS.category (ENUM: bread, cake, pastry, bun)
    //   selling_price: 60,        ← PRODUCTS.selling_price (DECIMAL 10,2)
    //   shelf_life_hours: 24,     ← PRODUCTS.shelf_life_hours (INT)
    //   unit: 'loaf',             ← PRODUCTS.unit (VARCHAR 20)
    //   is_active: true           ← PRODUCTS.is_active (BOOLEAN, default true)
    // }
  }
})

// ---------------------------------------------------------------
// EMIT: events this component sends UP to its parent
// The parent decides what to do — this component just reports.
// Think of it like a cashier reporting a sale to the manager.
// ---------------------------------------------------------------
const emit = defineEmits(['sell-product', 'view-recipe'])

// ---------------------------------------------------------------
// COMPUTED: derived values that auto-recalculate when data changes
// These implement BUSINESS RULES from the capstone spec:
// "Products have a shelf life in hours. Expiry is calculated from
//  batch completion time."
// ---------------------------------------------------------------
const shelfLifeStatus = computed(() => {
  const hours = props.product.shelf_life_hours
  if (hours <= 8) return { label: 'Very short shelf life', class: 'urgent', icon: '🔴' }
  if (hours <= 12) return { label: 'Sells fast — prioritize', class: 'warning', icon: '🟡' }
  if (hours <= 24) return { label: 'Sell today', class: 'today', icon: '🟠' }
  return { label: 'Multi-day shelf life', class: 'safe', icon: '🟢' }
})

// Format price with commas for readability: 1000 → 1,000
const formattedPrice = computed(() => {
  return props.product.selling_price.toLocaleString()
})

function handleSell() {
  emit('sell-product', props.product)
}

function handleViewRecipe() {
  emit('view-recipe', props.product.id)
}
</script>

<template>
  <div class="product-card" :class="{ 'inactive': !product.is_active }">
    <!-- Card header: product name + category badge -->
    <div class="card-header">
      <h3 class="product-name">{{ product.name }}</h3>
      <span :class="'category-badge category-' + product.category">
        {{ product.category }}
      </span>
    </div>

    <!-- Price display -->
    <div class="price-section">
      <span class="price">KES {{ formattedPrice }}</span>
      <span class="unit">per {{ product.unit }}</span>
    </div>

    <!-- Shelf life indicator — business rule visualization -->
    <div :class="'shelf-life shelf-' + shelfLifeStatus.class">
      <span class="shelf-icon">{{ shelfLifeStatus.icon }}</span>
      <span>{{ shelfLifeStatus.label }} ({{ product.shelf_life_hours }}h)</span>
    </div>

    <!-- Inactive product warning -->
    <div v-if="!product.is_active" class="inactive-banner">
      ⚠ Product inactive — not available for sale
    </div>

    <!-- Action buttons -->
    <div class="card-actions">
      <button class="btn-primary" @click="handleSell" :disabled="!product.is_active">
        Sell
      </button>
      <button class="btn-secondary" @click="handleViewRecipe">
        View Recipe
      </button>
    </div>
  </div>
</template>

<style scoped>
.product-card {
  background: white;
  border-radius: 12px;
  padding: 1.25rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border-left: 4px solid #2563EB;
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.product-card.inactive {
  opacity: 0.6;
  border-left-color: #9CA3AF;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.product-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1A1A2E;
  margin: 0;
}

.category-badge {
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
  white-space: nowrap;
}

.category-bread  { background: #FEF3C7; color: #92400E; }
.category-cake   { background: #FCE7F3; color: #9D174D; }
.category-pastry { background: #E0E7FF; color: #3730A3; }
.category-bun    { background: #D1FAE5; color: #065F46; }

.price-section {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

.price {
  font-size: 1.4rem;
  font-weight: 700;
  color: #E8541E;
}

.unit {
  font-size: 0.85rem;
  color: #9CA3AF;
}

.shelf-life {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.85rem;
  padding: 6px 10px;
  border-radius: 6px;
}

.shelf-icon { font-size: 0.9rem; }
.shelf-urgent  { background: #FEF2F2; color: #DC2626; }
.shelf-warning { background: #FFFBEB; color: #D97706; }
.shelf-today   { background: #FFF7ED; color: #C2410C; }
.shelf-safe    { background: #F0FDF4; color: #059669; }

.inactive-banner {
  background: #FEF2F2;
  color: #991B1B;
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 500;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: auto;
}

.btn-primary, .btn-secondary {
  flex: 1;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.85rem;
  font-weight: 500;
  transition: background 0.2s;
}

.btn-primary {
  background: #1A1A2E;
  color: white;
}

.btn-primary:hover { background: #E8541E; }
.btn-primary:disabled { background: #D1D5DB; cursor: not-allowed; }

.btn-secondary {
  background: #F3F4F6;
  color: #374151;
}

.btn-secondary:hover { background: #E5E7EB; }
</style>
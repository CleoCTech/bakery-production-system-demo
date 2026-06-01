<script setup>
import { ref, computed } from 'vue'

// ---------------------------------------------------------------
// PROPS: match PRODUCTION_BATCHES table from ERD
//
// In Week 6: GET /api/production-batches
// Response includes product_name from a JOIN with PRODUCTS table:
//   SELECT pb.*, p.name as product_name
//   FROM production_batches pb
//   JOIN products p ON pb.product_id = p.id
//
// This is why product_name is in the data — it comes from the JOIN,
// not from the PRODUCTION_BATCHES table itself.
// ---------------------------------------------------------------
const props = defineProps({
  batch: { type: Object, required: true }
  // Expected shape:
  // {
  //   id: 1,                          ← PK
  //   product_name: 'White Bread',    ← from JOIN with PRODUCTS
  //   product_id: 1,                  ← FK to PRODUCTS
  //   planned_quantity: 50,           ← how many we plan to make
  //   actual_quantity: 47,            ← how many we actually made (null if not done)
  //   wastage_quantity: 3,            ← how many were lost
  //   status: 'done',                 ← planned|mixing|baking|cooling|done|failed
  //   started_at: '2026-05-20 06:30', ← when mixing started
  //   completed_at: '2026-05-20 08:15' ← when marked done
  // }
})

</script>

<template>


</template>
<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-3">Items List</h2>

    <table class="w-full border border-gray-300 rounded-lg overflow-hidden shadow-sm">
  <thead class="bg-gray-100 text-left">
    <tr>
      <th class="p-3 border-b">Name</th>
      <th class="p-3 border-b">Quantity</th>
      <th class="p-3 border-b">Price</th>
      <th class="p-3 border-b">Total Price</th>
    </tr>
  </thead>

  <tbody>
    <!-- items থাকলে -->
    <template v-if="items.length">
      <tr 
        v-for="item in items" 
        :key="item.id"
        class="hover:bg-gray-50 transition"
      >
        <td class="p-3 border-b">{{ item.name }}</td>
        <td class="p-3 border-b">{{ item.quantity }}</td>
        <td class="p-3 border-b">{{ item.price }}</td>
        <td class="p-3 border-b font-semibold">{{ item.total_price }}</td>
      </tr>
    </template>

    <!-- items না থাকলে -->
    <tr v-else>
      <td colspan="4" class="text-center text-gray-500 py-4">
        No products available ( Seeder Implement Need )
      </td>
    </tr>
  </tbody>
</table>


  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

// Reactive items array
const items = ref([]);

// Load items from API
const loadItems = async () => {
  try {
    const res = await axios.get("/api/items");
    console.log(res);
    items.value = res.data;
  } catch (error) {
    console.error("Error loading items:", error);
  }
};

// Auto load on component mount
onMounted(() => {
  loadItems();
});
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: #f4f4f4;
}
</style>

<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-3 text-center mb-5 mt-3">Items List</h2>

    <table class="w-full bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
  <thead>
    <tr class="bg-gray-900 text-black text-left">
      <th class="p-4 text-lg font-bold tracking-wide uppercase">Item Name</th>
      <th class="p-4 text-lg font-bold tracking-wide uppercase">Total Stock</th>
      <th class="p-4 text-lg font-bold tracking-wide uppercase">Price</th>
    </tr>
  </thead>

  <tbody>
    <!-- items থাকলে -->
    <template v-if="items.length">
      <tr
        v-for="item in items"
        :key="item.id"
        class="border-b hover:bg-gray-100 transition even:bg-gray-50"
      >
        <td class="p-4 text-gray-800 font-medium">{{ item.name }}</td>
        <td class="p-4 text-gray-700">{{ item.quantity }}</td>
        <td class="p-4 text-gray-700">{{ item.price }}</td>
      </tr>
    </template>

    <!-- items না থাকলে -->
    <tr v-else>
      <td colspan="4" class="text-center text-gray-500 py-6 text-lg">
        No products available (Seeder Needed)
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

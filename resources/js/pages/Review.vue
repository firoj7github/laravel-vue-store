<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-4 text-center">Sell Items</h2>

    <!-- Multi Select -->
    <label>Select Products Here</label>
    <multiselect
      v-model="selectedProducts"
      :options="items"
      :multiple="true"
      :close-on-select="false"
      :clear-on-select="false"
      :preserve-search="true"
      label="name"
      track-by="id"
      placeholder="Select products"
      class="multiselect"
    />

    <!-- Sell Quantity -->
    <label>Sell Quantity:</label>
    <input 
      type="number" 
      v-model="sellQty" 
      class="border p-2 w-full mb-3"
      placeholder="Enter quantity to sell"
    />

    <!-- Auto Total Price -->
    <label>Total Price (Auto):</label>
    <input 
      type="text" 
      class="border p-2 w-full mb-3 bg-gray-100"
      :value="calculatedTotal"
      readonly
    />

    <!-- Submit -->
    <button 
      class="bg-blue-600 text-white px-4 py-2 rounded"
      @click="sellNow"
    >
      Sell Now
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
import { useToast } from 'vue-toastification';
const toast = useToast();


const items = ref([]);
const selectedProducts = ref([]); // multi select
const sellQty = ref(0);

// Load items from API
const loadItems = async () => {
  const res = await axios.get("/api/items");
  items.value = res.data;
};

onMounted(() => loadItems());

// Calculate Total Price
const calculatedTotal = computed(() => {
  if (!selectedProducts.value.length || !sellQty.value) return 0;

  let qty = sellQty.value;
  let total = 0;

  // FIFO → lowest ID first
  let sorted = [...selectedProducts.value].sort((a, b) => a.id - b.id);

  for (let item of sorted) {
    if (qty <= 0) break;

    let take = Math.min(item.quantity, qty);  
    total += take * item.price;
    qty -= take;
  }

  return total;
});


// Submit Sell
const sellNow = async () => {
  if (!selectedProducts.value.length || !sellQty.value) {
    alert("Select product and quantity");
    return;
  }

  const selected_ids = selectedProducts.value.map(p => p.id);

  const res = await axios.post("/api/sell", {
    quantity: sellQty.value,
    selected_ids, 
         
  });

  alert("Sell Done! Total Price = " + res.data.total_price);

  await loadItems();
  sellQty.value = 0;
  selectedProducts.value = [];
  toast.success("Sell Item successfully!");
};

</script>

<style scoped>
option {
  padding: 6px;
}
</style>

<template>
  <div class="p-4">

    <h2 class="text-xl font-bold mb-4 text-center">Sell Items</h2>

    <label>Select Products</label>
    <multiselect
      v-model="selectedDropdownProduct"
      :options="items"
      label="name"
      track-by="id"
      placeholder="Select products"
      class="multiselect mb-4"
      @select="addProductRow"
    />

    <!-- MULTIPLE ROWS -->
    <table class="w-full border">
      <thead>
        <tr class="bg-gray-100">
          <th class="border p-2 text-center">Product</th>
          <th class="border p-2 text-center">Total Stock</th>
          <th class="border p-2 text-center">Delivery Qty</th>
          <th class="border p-2 text-center">FIFO Breakdown</th>
          <th class="border p-2 text-center">Grand Total</th>
        </tr>
      </thead>

      <tbody>
        <tr 
          v-for="row in productRows" 
          :key="row.id"
        >
          <td class="border p-2 text-center font-bold">
            {{ row.name }}
          </td>

          <td class="border p-2 text-center">
            {{ row.totalStock }}
          </td>

          <td class="border p-2 text-center">
            <input 
              type="number"
              class="border p-1 w-full text-center"
              v-model.number="row.deliveryQty"
              min="0"
              :max="row.totalStock"
              @input="calculateFIFO(row)"
            />
          </td>

          <td class="border p-2 text-left">
            <div v-for="(b, index) in row.breakdown" :key="index">
              Lot {{ index+1 }} → {{ b.qty }} × {{ b.price }} = {{ b.total }}
            </div>
          </td>

          <td class="border p-2 text-center font-bold">
            {{ row.grandTotal }}
          </td>
        </tr>
      </tbody>
    </table>
<button 
  class="mt-4 bg-blue-600 text-white px-4 py-2 rounded"
  @click="sellItems"
>
  Sell Items
</button>

  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import Multiselect from "vue-multiselect";

const items = ref([]);
const selectedDropdownProduct = ref(null);
const productRows = ref([]);

// Load Items
const loadItems = async () => {
  const res = await axios.get("/api/items");
  items.value = res.data;

  if (items.value.length > 0) {
    addProductRow(items.value[0]);
  }
};
loadItems();

// Add product row
function addProductRow(product) {
  if (!product) return;

  if (productRows.value.find(p => p.id === product.id)) return;

  productRows.value.push({
    id: product.id,
    name: product.name,
    lots: product.lots,
    totalStock: product.quantity,
    deliveryQty: 0,
    breakdown: [],
    grandTotal: 0
  });
}

// FIFO calculator
function calculateFIFO(row) {
  let qty = row.deliveryQty;
  row.breakdown = [];
  row.grandTotal = 0;

  if (qty <= 0) return;

  let total = 0;
  const sortedLots = [...row.lots];

  sortedLots.forEach(lot => {
    if (qty <= 0) return;

    const take = Math.min(qty, lot.quantity);

    row.breakdown.push({
      qty: take,
      price: lot.price,
      total: take * lot.price
    });

    total += take * lot.price;
    qty -= take;
  });

  row.grandTotal = total;
}

// SELL API CALL (FINAL FIXED VERSION)
const sellItems = async () => {
  if (productRows.value.length === 0) {
    alert("Select products first!");
    return;
  }

  const payload = {
    items: productRows.value
      .filter(row => row.deliveryQty > 0)
      .map(row => ({
        product_id: row.id,
        quantity: row.deliveryQty
      }))
  };

  if (payload.items.length === 0) {
    alert("Please enter delivery quantity.");
    return;
  }

  try {
    const res = await axios.post("/api/sell-items", payload);

    alert("Items sold successfully!");

    // Refresh items
    const refreshed = await axios.get("/api/items");
    items.value = refreshed.data;

    // Update table rows
    productRows.value = productRows.value.map(row => {
      const updated = refreshed.data.find(p => p.id === row.id);

      return {
        ...row,
        totalStock: updated.quantity,
        lots: updated.lots,
        deliveryQty: 0,
        breakdown: [],
        grandTotal: 0
      };
    });

  } catch (e) {
    alert("Sell failed: " + e.response?.data?.message);
  }
};
</script>


<style>
.multiselect {
  width: 400px;
}
</style>

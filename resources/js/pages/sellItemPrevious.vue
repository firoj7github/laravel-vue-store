<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-4 text-center">Sell Items</h2>

    <label>Select Products</label>
    <multiselect
      v-model="selectedProducts"
      :options="items"
      :multiple="true"
      label="name"
      track-by="id"
      @update:modelValue="updateTotalStock"
      placeholder="Select products"
      class="multiselect mb-4"
    />

    <!-- Single Row Table -->
    <table class="w-full border">
      <thead>
        <tr class="bg-gray-100">
          <th class="border p-2 text-center">Total Stock</th>
          <th class="border p-2 text-center">Delivery Qty</th>
          <th class="border p-2 text-center">Price × Qty</th>
          <th class="border p-2 text-center">Grand Total Price</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="border p-2 text-center">{{ totalStock }}</td>

          <td class="border p-2 text-center">
            <input 
              type="number"
              v-model.number="deliveryQty"
              @input="calculatePrice"
              class="border p-1 w-full text-center"
              min="0"
              :max="totalStock"
            />
          </td>

          <!-- NEW COLUMN for Price × Qty -->
          <td class="border p-2 text-left">
            <ul>
              <li v-for="(line, i) in breakdown" :key="i">
                {{ line.name }} — {{ line.qty }} × {{ line.price }} = {{ line.total }}
              </li>
            </ul>
          </td>

          <td class="border p-2 text-center font-bold">
            {{ grandTotal }}
          </td>
        </tr>
      </tbody>
    </table>

    <button 
      class="bg-blue-600 text-white px-4 py-2 rounded mt-4"
      @click="submitSell"
    >
      Sell Now
    </button>

  </div>
</template>
<script setup>
import { ref } from "vue";
import axios from "axios";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
import { useToast } from 'vue-toastification';
const toast = useToast();

const items = ref([]);
const selectedProducts = ref([]);

const totalStock = ref(0);
const deliveryQty = ref(0);
const grandTotal = ref(0);

const breakdown = ref([]); // NEW ARRAY to show price * qty lines

// Load Items
const loadItems = async () => {
  const res = await axios.get("/api/items");
  items.value = res.data;
};
loadItems();

const updateTotalStock = () => {
  totalStock.value = selectedProducts.value.reduce(
    (sum, p) => sum + p.quantity,
    0
  );

  deliveryQty.value = 0;
  grandTotal.value = 0;
  breakdown.value = [];
};

// FIFO Price Calculation
const calculatePrice = () => {
  let qty = deliveryQty.value;

  // Stock Check
  if (qty > totalStock.value) {
    breakdown.value = [];
    grandTotal.value = 0;

    alert(`You cannot deliver ${qty}. Only ${totalStock.value} in stock.`);

    return; // STOP here
  }


  let total = 0;

  breakdown.value = []; // Reset breakdown

  let sorted = [...selectedProducts.value].sort((a, b) => a.id - b.id);

  sorted.forEach(p => {
    if (qty <= 0) return;

    const take = Math.min(qty, p.quantity);
    const lineTotal = take * p.price;

    breakdown.value.push({
      name: p.name,
      qty: take,
      price: p.price,
      total: lineTotal
    });

    total += lineTotal;
    qty -= take;
  });

  grandTotal.value = total;
};

// Submit Sell
const submitSell = async () => {
  const selected_ids = selectedProducts.value.map(p => p.id);

  const res = await axios.post("/api/sell-multiple", {
    quantity: deliveryQty.value,
    selected_ids
  });

  alert("Sell Completed! Total Price = " + grandTotal.value);

  loadItems();
  selectedProducts.value = [];
  totalStock.value = 0;
  deliveryQty.value = 0;
  grandTotal.value = 0;
  breakdown.value = [];
  toast.success("Sell Item successfully!");
};
</script>

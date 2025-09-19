<template>
  <v-layout>
    <v-card class="pa-4" min-width="400">
      <!-- Card Select -->
      <v-select
        :items="props.cardTemplate"
        item-title="card_name"
        item-value="id"
        v-model="form.template_id"
        variant="outlined"
        label="Select Card"
      />

      <!-- Available quantity -->
      <p v-if="quantityAvailable !== ''">
        Available: {{ quantityAvailable }}
      </p>

      <!-- Quantity input -->
      <v-text-field
        label="Quantity"
        v-model="form.quantity"
        variant="outlined"
      />

      <!-- Sold To input -->
      <v-text-field
        label="Sold To"
        variant="outlined"
        v-model="form.soldBy"
      />

      <!-- Submit button -->
      <v-btn color="blue-darken-4" @click="submitForm">
        Submit
      </v-btn>
    </v-card>
  </v-layout>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
  cardTemplate: Array
});

const quantityAvailable = ref('');

// const form = useForm({
//   template_id: null,
//   quantity: null,
//   soldBy: ''
// });

const form = ref({
  template_id: null,
  quantity: null,
  soldBy: '',
});

// Watch card selection and fetch available quantity dynamically
// watch(() => form.template_id, (newVal) => {
//   if (newVal) {
//     router
//       .get(route('available_cards.show', newVal), {}, { preserveState: true })
//       .then((response) => {
//         // The backend returns JSON, so read it directly
//         quantityAvailable.value = response.props.available_quantity ?? 0;
//       })
//       .catch(() => {
//         quantityAvailable.value = 'Error fetching quantity';
//       });
//   } else {
//     quantityAvailable.value = '';
//   }
// });


// Watch the selected card
watch(() => form.value.template_id, (newVal) => {
  if (!newVal) {
    quantityAvailable.value = '';
    return;
  }

  // Fetch available cards from backend
  fetch(route('available_cards.show', newVal))
    .then(res => res.json())
    .then(data => {
      quantityAvailable.value = data.available_quantity;
    })
    .catch(() => {
      quantityAvailable.value = 'Error fetching quantity';
    });
});

// Submit form
const submitForm = () => {
  form.post(route('sell-card.store')); // adjust route if needed
};
</script>

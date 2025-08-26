<template>
  <div style=" padding: 100px; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
    <div>
      <h1> {{ time }}</h1>
      <h1>{{ date }}</h1>

      <v-text-field
        v-model="form.PLATENO"
        placeholder="Enter Plate No."
        style="width: 300px"
        variant="underlined"
      />
      <span v-if="form.errors.PLATENO" style="color: red;">{{ form.errors.PLATENO }}</span>

      <v-btn size="x-large" @click="createTicket" block color="blue-darken-4" :disabled="form.processing">
        New Ticket
      </v-btn>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import dayjs from 'dayjs';
import { useForm } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia'; // Keep Inertia for back()

// Refs for live clock and plate number
const time = ref('');
const date = ref('');

// Interval ID for clearing on unmount
let intervalId;

// Function to update time and date every second
const updateTime = () => {
  const now = dayjs();
  time.value = now.format('hh:mm:ss ');
  date.value = now.format('MMMM D, YYYY');
};

// Start live clock on mount
onMounted(() => {
  updateTime(); // initial call
  intervalId = setInterval(updateTime, 1000); // update every second
});

// Clear interval on unmount
onUnmounted(() => {
  clearInterval(intervalId);
});

// Use useForm to manage form state and submission
const form = useForm({
  PLATENO: '',
  PARKYEAR: '',
  PARKMONTH: '',
  PARKDAY: '',
  PARKHOUR: '',
  PARKMINUTE: '',
  PARKSECOND: '',
});

// Function to create ticket
const createTicket = () => {
  // Get current timestamp and populate form fields
  const now = dayjs();
  form.PARKYEAR = now.year();
  form.PARKMONTH = now.month() + 1;
  form.PARKDAY = now.date();
  form.PARKHOUR = now.hour();
  form.PARKMINUTE = now.minute();
  form.PARKSECOND = now.second();

  // Submit the form
  form.post(route('parkin.store'), {
    onSuccess: () => {
      // Reset the plate number input after a successful submission
      form.reset('PLATENO');
    },
    onError: () => {
      // You can handle errors here, if any
      // Errors are automatically populated in form.errors
    }
  });
};
</script>
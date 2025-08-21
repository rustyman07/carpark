<template>
  <div style=" padding: 100px; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
<div>
      <h1> {{ time }}</h1>
    <h1>{{ date }}</h1>

    <v-text-field
      v-model="plateNo"
      placeholder="Enter Plate No."
      style="width: 300px"
      variant="underlined"
    />

    <v-btn size="x-large" @click="createTicket"  block color="blue-darken-4">
      New Ticket
    </v-btn >
</div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import dayjs from 'dayjs';
import { Inertia } from '@inertiajs/inertia';

// Refs for live clock and plate number
const time = ref('');
const date = ref('');
const plateNo = ref('');

// Interval ID for clearing on unmount
let intervalId;

// Function to update time and date every second
const updateTime = () => {
  const now = dayjs();
//  time.value = now.format('hh:mm:ss A');   // 12-hour clock with seconds
    time.value = now.format('hh:mm:ss '); 
  date.value = now.format('MMMM D, YYYY'); // e.g., August 20, 2025
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

// Function to create ticket
const createTicket = () => {
  if (!plateNo.value) {
    alert('Plate number is required!');
    return;
  }

  const now = dayjs(); // current timestamp

  // Send to Laravel via Inertia
  Inertia.post(route('parkin.store'), {
    PLATENO: plateNo.value,
    PARKYEAR: now.year(),
    PARKMONTH: now.month() + 1, // months are 0-indexed
    PARKDAY: now.date(),
    PARKHOUR: now.hour(),
    PARKMINUTE: now.minute(),
    PARKSECOND: now.second(),
  });

  // Optional: reset input
  plateNo.value = '';
};
</script>

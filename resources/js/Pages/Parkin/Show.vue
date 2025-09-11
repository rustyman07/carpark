<template>
  <v-container class="d-flex justify-center mt-10">
    <v-card class="pa-6" max-width="400">
      <div class="text-center">
        <qrcode-vue :value="qrValue" :size="200" level="H" />
      </div>

      <v-divider class="my-4"></v-divider>

      <div class="text-center">
        <h2 class="font-weight-bold">Ticket No: {{ ticket.TICKETNO }}</h2>
        <p class="text-h6">Plate No: {{ ticket.PLATENO }}</p>
        <p>Date Park In: {{ formattedDate }}</p>
      </div>
    </v-card>
  </v-container>
</template>

<script setup>
import QrcodeVue from 'qrcode.vue'
import dayjs from 'dayjs'

// Props from Laravel Inertia
const props = defineProps({
  ticket: Object
})

// Generate QR Code content (could be ticket ID, or full JSON)
const qrValue =  props.ticket.QRCODE;
// Format date
const formattedDate = dayjs(
  `${props.ticket.PARKYEAR}-${props.ticket.PARKMONTH}-${props.ticket.PARKDAY} ${props.ticket.PARKHOUR}:${props.ticket.PARKMINUTE}:${props.ticket.PARKSECOND}`
).format('MMMM D, YYYY hh:mm: A')
</script>

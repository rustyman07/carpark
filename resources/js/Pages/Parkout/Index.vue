<template>
  <div style="padding: 100px; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
    <div>
      <h1 v-if="success && ticket">Ticket Fee: {{ ticket.PARKFEE }}</h1>

      <v-text-field
        class="text-h1"
        v-model="ticketNo"
        placeholder="Enter Ticket No."
        style="width: 300px"
        variant="underlined"
      />
      <v-btn size="x-large" @click="createTicket" block color="blue-darken-4">
        Submit
      </v-btn>

      <!-- Debug -->
      <pre>{{ ticket }}</pre>
      <pre>success: {{ success }}</pre>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import dayjs from 'dayjs';
import { Inertia } from '@inertiajs/inertia';

const page = usePage();

const ticket = computed(() => page.props.ticket || null);
const success = computed(() => page.props.success || false);

const ticketNo = ref('');

const createTicket = () => {
  const now = dayjs();

  Inertia.post(route('submit.parkout'), {
    TICKETNO: ticketNo.value,
    PARKOUTYEAR: now.year(),
    PARKOUTMONTH: now.month() + 1,
    PARKOUTDAY: now.date(),
    PARKOUTHOUR: now.hour(),
    PARKOUTMINUTE: now.minute(),
    PARKOUTSECOND: now.second(),
  });

  ticketNo.value = '';
};
</script>

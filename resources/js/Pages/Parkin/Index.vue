<template>
  <v-container class="fill-height d-flex flex-column align-center  justify-center" >
       
        <div class="text-h5 text-md-h3 text-center text-grey-darken-2 mb-2">{{ date }}</div>
        <div class="text-h2 text-md-h1 font-weight-bold opacity-70 text-center"> {{ time  }} <span style="font-size: 2.5rem;">{{ AMPM }}</span></div>

        <v-container   max-width ="450">
            <v-text-field
            v-model="form.plate_no"
            placeholder="Enter Plate No." 
            variant="underlined"
            :error-messages="form.errors.plate_no"
            @input="form.plate_no = form.plate_no.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()"
            class="text-center-input "
            />
            
            <v-layout class="d-flex flex-column ga-2">
                <v-btn size="x-large" @click="createTicket" block color="blue-darken-4"   :disabled="form.processing || !form.plate_no">
                    New Ticket
                </v-btn>
                <v-layout class="d-flex ga-2 align-center">
                    <v-divider thickness="2"></v-divider>   <span>OR</span><v-divider thickness="2"></v-divider>
                </v-layout>     
                <v-btn size="x-large" @click="createTicket" block color="blue-darken-4" disabled>
                    Scan 
                </v-btn>

            </v-layout>

        </v-container>
        
        



    
  </v-container>
</template>

<script setup>
import { ref, onMounted, onUnmounted,watch } from 'vue';
import dayjs from 'dayjs';
import { useForm } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia'; // Keep Inertia for back()

// Refs for live clock and plate number
const time = ref('');
const date = ref('');
const AMPM = ref('');



// Interval ID for clearing on unmount
let intervalId;

// Function to update time and date every second
const updateTime = () => {
  const now = dayjs();
  time.value = now.format('hh:mm:ss ');
  date.value = now.format('MMMM D, YYYY');
  AMPM.value = now.format('A')
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
  plate_no: '',
  park_year: '',
  park_month: '',
  park_day: '',
  park_hour: '',
  park_minute: '',
  park_second: '',
});

// Function to create ticket
const createTicket = () => {
  // Get current timestamp and populate form fields
  const now = dayjs();
  form.park_year = now.year();
  form.park_month = now.month() + 1;
  form.park_day = now.date();
  form.park_hour = now.hour();
  form.park_minute = now.minute();
  form.park_second = now.second();

  // Submit the form
  form.post(route('parkin.store'), {
    onSuccess: () => {
      // Reset the plate number input after a successful submission
      form.reset('plate_no');
    },
    onError: () => {
      // You can handle errors here, if any
      // Errors are automatically populated in form.errors
    }
  });
};

console.log('test');
</script>

<style scoped>

.text-center-input ::v-deep input {
  text-align: center;
    font-size: 2.5rem;
  font-weight: bold;
    color: #616161;
}
/* .time {
  margin-top: -20px;
  font-size: 6em;
  font-weight: bold;
  color: #616161;

} */
</style>
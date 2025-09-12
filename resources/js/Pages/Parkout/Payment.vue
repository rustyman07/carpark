<template>

  <v-dialog v-model="isScanQR" max-width="600">
    <v-card>
      <v-card-title>Scan QR Code</v-card-title>
      <v-card-text>
        <div id="reader" style="width:100%; height:400px;"></div>
      </v-card-text>
      <v-card-actions>
        <v-btn color="error" text @click="closeScanner">Close</v-btn>
      </v-card-actions>
    </v-card>
    </v-dialog>
    <v-container class="d-flex justify-center align-center fill-height" >
        <v-card  class="pa-5" >
        <v-card-text class="py-1 px-2">
            Ticket No: {{ ticket.data.ticket_no }}
        </v-card-text>
        <v-card-text class="py-1 px-2">
            Park in Date Time: {{ formatDate(ticket.data.park_datetime) }}
        </v-card-text>
        <v-card-text class="py-1 px-2">
            Park out Date Time: {{ formatDate(ticket.data.parkout_datetime) }}
        </v-card-text>
            <v-card-text v-if="ticket.data.days_parked !== null" class="py-1 px-2">
            No of Days: {{ ticket.data.days_parked }}
            </v-card-text>

             <v-card flat  elevation= 3 class="mt-4">
            <v-data-table-server
                :headers="headers"
                :items="items"
                :items-per-page="5"
                :loading="false"
                class="elevation-1"
                disable-sort
                 hide-default-footer
                
                >
                  <template v-slot:top>
                    <!-- <v-toolbar flat>
                      <v-toolbar-title>Card Transactions</v-toolbar-title>
                    </v-toolbar> -->
                    <v-btn  color="blue-darken-4" @click="scanQR">
                        <v-icon left class="mr-4">mdi-qrcode-scan</v-icon>
                        Scan QR to use card
                    </v-btn>
                  </template>


            </v-data-table-server>
             </v-card>


                <v-card-text class="py-1 px-2">
            Ticket Fee: {{  formatCurrency(ticket.data.park_fee) }}
        </v-card-text>


            <v-layout class="d-flex mt-2 ga-4 ">
                <v-btn  class="block" @click="submitPayment" color="blue-darken-4">Payment</v-btn>
                <!-- <v-btn @click="scanQR" color="blue-darken-4">Scan QR</v-btn>-->
                <v-btn @click="cancelPayment" color="red-darken-2">Cancel</v-btn> 
            </v-layout>



        </v-card>
</v-container>
</template>

<script setup>
import { computed, ref, onBeforeUnmount } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { route } from "ziggy-js"
import { Html5Qrcode } from 'html5-qrcode';
import dayjs from 'dayjs';
import { formatCurrency } from '../../utils/utility';

// Props
const props = defineProps({
  ticket: Object,
  cardsTrans: Array, // from backend if needed
});

// ----------------------
// State
// ----------------------
const isScanQR = ref(false);
const html5QrCode = ref(null);

const scannedCards = ref([]); // 👈 store scanned results here

// Data table headers
const headers = [
  { key: 'card_number', title: 'Card Number' },
  { key: 'no_of_days', title: 'No. of Days' },
  { key: 'price', title: 'Price' },
  { key: 'balance', title: 'Balance' },
];

// Data table items
const items = computed(() => {
  return scannedCards.value.map(item => ({
    card_number: item.card_number,
    no_of_days: item.no_of_days,
    price: formatCurrency(item.price),
    balance: item.balance,
  }));
});

// ----------------------
// Helpers
// ----------------------
const formatDate = (date) => {
  return date ? dayjs(date).format('MM/DD/YYYY') : 'N/A';
};

// ----------------------
// Methods
// ----------------------
const submitPayment = () => {
  const form = useForm({
    ticket_id: props.ticket.data.id,
    cards: scannedCards.value, // send array
  });

  form.post(route('store.payment'), {
    onSuccess: () => {
      scannedCards.value = []; // clear after success
    },
  });
};

const cancelPayment = () => {
  router.get(route('parkout.index'));
};

const scanQR = async () => {
  isScanQR.value = true;
  setTimeout(startScanner, 300);
};

const startScanner = async () => {
  if (!isScanQR.value) return;

  try {
    html5QrCode.value = new Html5Qrcode('reader');
    await html5QrCode.value.start(
      { facingMode: 'environment' },
      { fps: 10, qrbox: { width: 250, height: 250 } },
      async (decodedText) => {
        console.log('QR code detected ✅', decodedText);

        await closeScanner();

        // Send to backend to get card details
        router.post(route('scan.qr.cards'), {
          qr_code: decodedText,
          ticket_id: props.ticket.data.id,
        }, {
          onSuccess: (page) => {
            // ✅ Assume backend returns the scanned card in flash or props
            if (page.props.card) {
              scannedCards.value.push(page.props.card);
            }
          }
        });
      }
    );
  } catch (err) {
    console.error('Error starting camera:', err.message);
  }
};

const closeScanner = async () => {
  if (html5QrCode.value) {
    try {
      await html5QrCode.value.stop();
      await html5QrCode.value.clear();
    } catch (e) {
      console.error('Error closing scanner:', e);
    }
    html5QrCode.value = null;
  }
  isScanQR.value = false;
};

// ----------------------
// Lifecycle
// ----------------------
onBeforeUnmount(() => {
  closeScanner();
});
</script>

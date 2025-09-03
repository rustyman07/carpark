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

        <v-card  class="pa-5">
        <v-card-text class="py-1 px-2">
            Park in Datess Time: {{ formatDate(ticket.data.park_datetime) }}
        </v-card-text>
        <v-card-text class="py-1 px-2">
            Park out Date Time: {{ formatDate(ticket.data.parkout_datetime) }}
        </v-card-text>
        <v-card-text class="py-1 px-2">
            Ticket Fee: {{ formatFee(ticket.data.park_fee) }}
        </v-card-text>

            <v-layout class="d-flex justify-space-between mt-2 ga-4">
                <v-btn @click="submitPayment" color="blue-darken-4">Cash</v-btn>
                <v-btn @click="scanQR" color="blue-darken-4">Scan QR</v-btn>
                <v-btn @click="cancelPayment" color="red-darken-2">Cancel</v-btn>
            </v-layout>
        </v-card>
</template>

<script setup>
import { computed, ref, watch, onBeforeUnmount } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { route } from "ziggy-js"
import { Html5Qrcode } from 'html5-qrcode';
import dayjs from 'dayjs';

// ----------------------
//
// Props and State
//
// ----------------------
const page = usePage();

const props = defineProps({
    ticket: Object,
});

const form = useForm({
    ticket_id: props.ticket.data.id,
    mode_of_payment : 'cash'
});

const isScanQR = ref(false);
const html5QrCode = ref(null);

// ----------------------
//
// Computed Properties
//
// ----------------------


// ----------------------
//
// Helper Functions
//
// ----------------------
const formatDate = (date) => {
    // Check if the date is a valid, truthy value before formatting
    return date ? dayjs(date).format('MM/DD/YYYY') : 'N/A';
};
const formatFee = (fee) => `${Number(fee).toFixed(2)} PHP`;

// ----------------------
//
// Methods
//
// ----------------------
const submitPayment = () => {
    form.post(route('store.payment'), {
        onSuccess: () => {
            // No need for a separate message object, Inertia handles success flashes
        },
        onError: (errors) => {
            // Inertia provides `errors` object for validation failures
            console.error('Payment failed:', errors);
        },
    });
};

const cancelPayment = () => {
    // You can add a confirmation dialog here if needed
    router.get(route('parkout.index'));
};

const scanQR = async () => {
    isScanQR.value = true;
    // Delay scanner start to allow the modal/component to render
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
                router.post(route('store.payment'), {
                    qr_code: decodedText,
                    ticket_id: props.ticket.id,
                    mode_of_payment : 'card'
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
//
// Lifecycle Hooks
//
// ----------------------
onBeforeUnmount(() => {
    closeScanner();
});
</script>
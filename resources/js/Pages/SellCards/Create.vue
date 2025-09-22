<template>
  <QrScanner v-model="isScanQR" :closeScanner="closeScanner" />

  <v-container class="d-flex justify-center align-center fill-height">
    <v-card class="pa-6" max-width="480" elevation="10" rounded="lg">
      
      <!-- Header -->
      <v-card-title class="text-h6 font-weight-bold text-center pb-2">
        Sell Card Payment
      </v-card-title>
      <v-divider></v-divider>

      <!-- Scan Button -->
      <div class="d-flex justify-center my-5">
        <v-btn color="blue-darken-4" size="large" variant="flat" @click="scanQR">
          <v-icon left>mdi-qrcode-scan</v-icon>
          Scan QR
        </v-btn>
      </div>

      <!-- Scanned Cards -->
      <div v-if="scannedCards.length" class="mb-4">
        <v-card
          v-for="card in scannedCards"
          :key="card.id"
          variant="outlined"
          class="pa-3 mb-2 d-flex align-center justify-space-between"
        >
          <div class="d-flex align-center">
            <v-icon color="blue-darken-3" size="28" class="mr-3">mdi-credit-card</v-icon>
            <div>
              <div class="text-body-2 font-weight-medium">{{ card.card_number }}</div>
              <div class="text-caption text-grey">{{ card.card_name }}</div>
            </div>
          </div>
          <div class="text-body-2 font-weight-bold">{{ card.price }}</div>
        </v-card>
      </div>
      <div v-else class="text-center text-grey text-caption mb-4">
        No cards scanned yet
      </div>

      <v-divider></v-divider>

      <!-- Payment Section -->
      <v-card-text class="pt-4">
        <v-text-field
          v-model="cashAmount"
          label="Cash Amount"
          type="number"
          variant="outlined"
          density="comfortable"
          prepend-inner-icon="mdi-cash"
        />
      </v-card-text>

      <!-- Actions -->
      <v-card-actions class="mt-2">
        <v-row dense class="w-100">
          <v-col cols="6">
            <v-btn 
              block 
              color="blue-darken-4" 
              variant="flat"
              @click="submitPayment"
              :disabled="!cashAmount && !scannedCards.length"
            >
              Pay
            </v-btn>
          </v-col>
          <v-col cols="6">
            <v-btn 
              block 
              color="red-darken-2" 
              variant="text" 
              @click="cancelPayment"
            >
              Cancel
            </v-btn>
          </v-col>
        </v-row>
      </v-card-actions>

    </v-card>
  </v-container>
</template>


<script setup>
import { router, useForm, } from '@inertiajs/vue3';
import { ref, watch ,computed, onBeforeUnmount} from 'vue';
import { Html5Qrcode } from 'html5-qrcode'
import { route } from 'ziggy-js';
import QrScanner from '../../Components/QrScanner.vue';
import axios from 'axios'
const props = defineProps({
  cardTemplate: Array,
  scannedCards : Array
});

const quantityAvailable = ref('');
const isScanQR = ref(false);
const html5QrCode = ref(null)

// const form = useForm({
//   template_id: null,
//   quantity: null,
//   soldBy: ''
// });

// const form = ref({
//   card_number: null,
//   price: null,
//   soldBy: '',
// });
const headers = [
  { key: 'card_number', title: 'Card Number' },
  { key: 'no_of_days', title: 'No. of Days' },
  { key: 'price', title: 'Price' },
  { key: 'balance', title: 'Balance', class: 'bg-blue-darken-4' },
]

const cards = ref([]);
const cashAmount = ref(null)
const scannedCards = computed(() => props.scannedCards || [])
// const cards = computed(() => {
//   return scannedCards.value.map(card => ({
//     card_number: card.card_number,
//     no_of_days: card.no_of_days,
//     price: formatCurrency(card.price),
//     balance: card.balance,
//   }))
// })

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
// watch(() => form.value.card_template_id, (newVal) => {
//   if (!newVal) {
//     quantityAvailable.value = '';
//     return;
//   }

//   // Fetch available cards from backend
//   fetch(route('available_cards.show', newVal))
//     .then(res => res.json())
//     .then(data => {
//       quantityAvailable.value = data.available_quantity;
//     })
//     .catch(() => {
//       quantityAvailable.value = 'Error fetching quantity';
//     });
// });


const scanQR = async () => {
  isScanQR.value = true
  setTimeout(startScanner, 300)
}

const startScanner = async () => {
  if (!isScanQR.value) return

  try {
    html5QrCode.value = new Html5Qrcode('reader')
    await html5QrCode.value.start(
      { facingMode: 'environment' },
      { fps: 10, qrbox: { width: 250, height: 250 } },
      async (decodedText) => {
        console.log('QR code detected ✅', decodedText)

        await closeScanner()

        // Send QR to backend -> backend updates session -> Inertia reloads page with updated cards & totals
        router.post(route('scan.qr.cards'), {
 qr_code: decodedText,
            is_sell_card: true,
        })
      }
    )
  } catch (err) {
    console.error('Error starting camera:', err.message)
  }
}


const submitPayment = () => {
    console.log('Submitting payment with cards:', cards.value)
  const form = useForm({
    cash_amount: cashAmount.value,
    cards: cards.value.map(c => c.id),
  })

  form.post(route('sell-card.payment'), {
    onSuccess: () => {
        
    //   router.get(route('parkout.index'))
    },
  })
}







const cancelPayment = () => {
  router.get(route('parkin.index'))
}


const closeScanner = async () => {
  if (html5QrCode.value) {
    try {
      await html5QrCode.value.stop()
      await html5QrCode.value.clear()
    } catch (e) {
      console.error('Error closing scanner:', e)
    }
    html5QrCode.value = null
  }
  isScanQR.value = false
}

// ----------------------
// Lifecycle
// ----------------------
onBeforeUnmount(() => {
  closeScanner()
})

</script>

<style scoped>

.text-caption{
    line-height: 1rem;
}
.text-caption1{
    font-size: 14px;
    line-height: 1rem;
}
</style>
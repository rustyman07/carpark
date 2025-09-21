<template>
    <QrScanner   v-model="isScanQR" :closeScanner = "closeScanner" />
    <!-- <v-layout>
    <v-card class="pa-4" min-width="400">

    <v-select
        :cards="props.cardTemplate"
        card-title="card_name"
        card-value="id"
        v-model="form.card_template_id"
        variant="outlined"
        label="Select Card"
    /> 
            <v-text-field
        label="Card Number"
        v-model="form.card_number"
        variant="outlined"
    />

    <v-text-field
        label="Price"
        v-model="form.price"
        variant="outlined"
    />



    <v-btn color="blue-darken-4" @click="submitForm">
        Submit
    </v-btn>
    <v-btn color="blue-darken-4" @click="scanQR">
        Scan QR
    </v-btn>
    </v-card>
    </v-layout> -->

    
  <v-container class="d-flex justify-center align-center fill-height">
    <v-card class="pa-5" min-width="350">
      <!-- Ticket Info -->
      <!-- <v-card-text class="py-1 px-2">
        Ticket No: {{ ticket.data.ticket_no }}
      </v-card-text>
      <v-card-text class="py-1 px-2">
        Park in Date Time: {{ formatDate(ticket.data.park_datetime) }} <span>{{ parkinTime }}</span>
      </v-card-text>
      <v-card-text class="py-1 px-2">
        Park out Date Time: {{ formatDate(ticket.data.parkout_datetime) }}  <span>{{ parkoutTime }}</span>
      </v-card-text>
      <v-card-text v-if="ticket.data.days_parked !== null" class="py-1 px-2">
        No of Days: {{ ticket.data.days_parked }}
      </v-card-text>
      <v-card-text v-if="hoursPark !== null" class="py-1 px-2">
        No of Hours: {{ hoursPark }}
      </v-card-text>
      <v-card-text class="py-1 px-2">
        Total Bill: {{ formatCurrency(ticket.data.park_fee) }}
      </v-card-text> -->

      <!-- Totals -->

      <!-- Scanned Cards -->
        <div class="flex- align-right">
                      <v-btn color="blue-darken-4 mb-4" @click="scanQR">
              <v-icon left class="mr-4">mdi-qrcode-scan</v-icon>
              Scan 
            </v-btn>

        </div>
  
   
     
        <!-- <v-data-table-server
       class="border border-gray-300 rounded"
         density="compact"
          :headers="headers"
          :cards="cards"
          :cards-per-page="5"
          :loading="false"
       flat
          disable-sort
          hide-default-footer
        >

      
       
        </v-data-table-server> -->

  <v-card v-for="card in cards" flat rounded class="border pa-1 mb-2 ">
    <div class="d-flex pr-2 ps-2 " >
        <div>
            <v-icon size="25" class="mr-2 d-flex  opacity-80  h-100 ">mdi-card</v-icon>
        </div>
         
    <div class="d-flex justify-space-between w-100" >
        <div class="d-flex flex-column ps-1">
            <div class="text-caption 1 opacity-80 ">{{ card.card_number }}</div>
            <div class="text-caption1 font-italic opacity-80 font-weight-bold ">{{ card.card_name }}</div>
        </div>
    <div class=" d-flex flex-column font-weight-bold justify-center  opacity-80 ">{{ card.price }}</div>
    </div>   
    </div>



  </v-card>

 
    <!-- <v-text-field
        label="Sold To"
        variant="outlined"
        v-model="form.soldBy"
    /> -->

   <!-- <v-card-text 
   class="py-1 px-2"
   >
       Cash
      </v-card-text> -->
   <v-text-field
     label="Cash"
        v-model="cashAmount"
        variant="outlined"
        type="number"
        :disabled="disAbledPayment"
        ></v-text-field> 

      <!-- Actions -->
<v-row>
  <v-col cols="6">
    <v-btn   @click="submitPayment"  color="blue-darken-4">Pay</v-btn>
  </v-col>
  <v-col cols="6">
    <v-btn block @click="cancelPayment" color="red-darken-2">Cancel</v-btn>
  </v-col>
</v-row>



        
     
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
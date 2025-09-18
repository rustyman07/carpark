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

  <v-container class="d-flex justify-center align-center fill-height">
    <v-card class="pa-5">
      <!-- Ticket Info -->
      <v-card-text class="py-1 px-2">
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
      </v-card-text>

      <!-- Totals -->

      <!-- Scanned Cards -->
        <div class="flex- align-right">
                      <v-btn color="blue-darken-4 mb-4" @click="scanQR">
              <v-icon left class="mr-4">mdi-qrcode-scan</v-icon>
              Scan QR to use card
            </v-btn>

        </div>
  
   
     
        <v-data-table-server
       class="border border-gray-300 rounded"
         density="compact"
          :headers="headers"
          :items="items"
          :items-per-page="5"
          :loading="false"
       flat
          disable-sort
          hide-default-footer
        >
       
        </v-data-table-server>

        <v-card-text class="py-1 px-2 mt-2">
        Total Covered by Cards: {{ formatCurrency(totalCovered) }}
      </v-card-text>
      <v-card-text class="py-1 px-2">
       Total: {{ formatCurrency(cashNeeded) }}
      </v-card-text>
   <v-card-text 
   class="py-1 px-2"
   >
       Cash
      </v-card-text>
    
        <v-text-field
        v-model="cashAmount"
        variant="outlined"
        type="number"
        :disabled="disAbledPayment"
        ></v-text-field>

      <!-- Actions -->
<v-row>
  <v-col cols="6">
    <v-btn     :disabled="scannedCards.length === 0 && !cashAmount" block @click="submitPayment" color="blue-darken-4">Pay</v-btn>
  </v-col>
  <v-col cols="6">
    <v-btn block @click="cancelPayment" color="red-darken-2">Cancel</v-btn>
  </v-col>
</v-row>

        
     
    </v-card>
  </v-container>
</template>

<script setup>
import { computed, ref, onBeforeUnmount } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { Html5Qrcode } from 'html5-qrcode'
import dayjs from 'dayjs'
import { formatCurrency } from '../../utils/utility'

// Props coming from Laravel Inertia
const props = defineProps({
  ticket: Object,
  scannedCards: Array,
  totalCovered: Number,
  cashNeeded: Number,
})

// ----------------------
// State
// ----------------------
const isScanQR = ref(false)
const html5QrCode = ref(null)
const cashAmount = ref(null)
const scannedCards = computed(() => props.scannedCards || [])
const totalCovered = computed(() => props.totalCovered || 0)
const cashNeeded   = computed(() => props.cashNeeded || 0)


const hoursPark = computed(()=>{

       return props.ticket.data.hours_parked 
})


const parkinTime = computed(() => {
  return props.ticket.data.park_datetime
    ? dayjs(props.ticket.data.park_datetime).format("hh:mm A") 
    : null
})
const parkoutTime = computed(() => {
  return props.ticket.data.parkout_datetime
    ? dayjs(props.ticket.data.parkout_datetime).format("hh:mm A") 
    : null
})


const disAbledPayment = computed(()=>{
    if (totalCovered.value === props.ticket.data.park_fee){
        return true;
    }
    else return false;
})



// Data table headers
const headers = [
  { key: 'card_number', title: 'Card Number' },
  { key: 'no_of_days', title: 'No. of Days' },
  { key: 'price', title: 'Price' },
  { key: 'balance', title: 'Balance', class: 'bg-blue-darken-4' },
]

// Data table items
const items = computed(() => {
  return scannedCards.value.map(item => ({
    card_number: item.card_number,
    no_of_days: item.no_of_days,
    price: formatCurrency(item.price),
    balance: item.balance,
  }))
})

// ----------------------
// Helpers
// ----------------------
const formatDate = (date) => {
  return date ? dayjs(date).format('MM/DD/YYYY') : 'N/A'
}

// ----------------------
// Methods
// ----------------------
const submitPayment = () => {
    console.log('Submitting payment with cards:', scannedCards.value)
  const form = useForm({
    ticket_id: props.ticket.data.id,
    hours_parked: props.ticket.data.hours_parked,
    days_parked: props.ticket.data.days_parked,
    cash_amount: cashAmount.value,
    cards: scannedCards.value.map(c => c.id),
  })

  form.post(route('store.payment'), {
    onSuccess: () => {
        
    //   router.get(route('parkout.index'))
    },
  })
}

const cancelPayment = () => {
  router.get(route('parkout'))
}

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
          ticket_id: props.ticket.data.id,
           ticket_uuid: props.ticket.data.uuid,
        })
      }
    )
  } catch (err) {
    console.error('Error starting camera:', err.message)
  }
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
 .v-table :deep thead{
  background-color:#757575; /* blue */
  color: white;            /* text color */
}

</style>

/* .v-field :deep input {
  border-color: red!important;
  box-shadow: none !important;
} */


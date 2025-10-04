<template>
  <QrScanner v-model="isScanQR" :closeScanner="closeScanner" />

  <v-container class="d-flex justify-center align-center fill-height">
    <v-card class="pa-6"  max-width="480" width="400" elevation="1" rounded="lg">

      <!-- Autocomplete -->
<v-autocomplete
  class="small-label"
  v-model="selected"
  v-model:search="search"
  :items="items"
  item-title="card_number"
  item-value="id"
  label="Search Card Number"
  density="compact"
  :loading="loading"
  hide-no-data
  hide-details
  variant="outlined"

/>


      
      <v-divider> <div class="text-caption" >OR</div></v-divider>

      <!-- Scan Button -->
   
        <v-btn class="mb-2" color="indigo-darken-4" block size="large" variant="flat" @click="scanQR">
          <v-icon class="mr-2">mdi-qrcode-scan</v-icon>
          Scan QR
        </v-btn>


      <!-- Scanned Cards -->
<div v-if="cards.length" class="mb-4">
  <div
    v-for="card in cards"
    :key="card.id"
    class="pa-3 border mb-2 d-flex align-center justify-space-between"
    style="position: relative;"            
  >
    <!-- Left -->
    <div class="flex align-center">
      <div class="flex justify-center items-center mr-3">
        <v-icon size="28">mdi-qrcode</v-icon>
      </div>
      <div>
        <div class="text-sm font-weight-medium">{{ card.card_number }}</div>
        <div class="text-caption text-grey">{{ card.card_name }}</div>
      </div>
    </div>

    <!-- Price -->
    <div class="text-body-2 font-weight-bold">{{ card.price }}</div>

    <!-- close -->
    <v-icon
color="red-darken-4"
      size="18"
      @click="removeCard(card.id)"
      style="position: absolute; top: 4px; right: 5px; cursor: pointer;"
    >
      mdi-close
    </v-icon>
  </div>
</div>


      <div v-else class="text-center text-grey text-caption mb-4">
        No cards scanned yet
      </div>



        <v-divider></v-divider>
<div class="my-2">Total: {{ new Intl.NumberFormat('en-US').format(total) }}</div>




      <!-- Payment Section -->
     
        <v-text-field
        class="small-label"
          v-model="form.cash_amount"
          label="Cash Amount"
          type="number"
          variant="outlined"
          density="comfortable"
          prepend-inner-icon="mdi-cash"
        />
          <v-text-field
        class="small-label"
          v-model="form.customer"
          label="Sold to"
         
          variant="outlined"
          density="comfortable"

        />
      
      <!-- Actions -->
      <v-card-actions class="mt-2">
        <v-row dense class="w-100">
          <v-col cols="6">
            <v-btn 
              block 
              color="indigo-darken-4" 
              variant="flat"
              @click="submitPayment"
              :disabled="payDisabled"
            >
              Pay
            </v-btn>
          </v-col>
          <v-col cols="6">
            <v-btn 
              block 
              color="grey-darken-2" 
              variant="flat" 
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
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { Html5Qrcode } from 'html5-qrcode';
import { route } from 'ziggy-js';
import QrScanner from '../../Components/QrScanner.vue';
import axios from 'axios';


// ----------------------
// Props & Refs
// ----------------------
const props = defineProps({
  cardTemplate: Array,
  scannedCards: Array
});


// ----------------------
// Autocomplete Setup
// ----------------------
const selected = ref(null);
const items = ref([]);
const cards = ref([]);
const loading = ref(false);
const search = ref(''); // Keep this ref to bind with v-model:search


const isScanQR = ref(false);
const html5QrCode = ref(null);


// const scannedCards = computed(() => props.scannedCards || []);



const form = useForm({
    customer: '',
    cash_amount : null,
    cards: []
})

const payDisabled = computed(() => {
  // Disable Pay if there are NO cards or cash_amount is empty/0
  return cards.value.length === 0 || !form.cash_amount || !form.customer
})

// Simple debounce function
function debounce(func, delay = 300) {
  let timer;
  return function(...args) {
    clearTimeout(timer);
    timer = setTimeout(() => func.apply(this, args), delay);
  };
}

// Fetch cards from backend
const fetchCards = debounce((val) => {
  if (!val) {
    items.value = [];
    loading.value = false;
    return;
  }

  loading.value = true;
  axios.get(route('card.inventory.search'), { params: { q: val } })
    .then(res => {
      items.value = res.data;
    })
    .finally(() => {
      loading.value = false;
    });
}, 300);

// Watch the search input from v-autocomplete
watch(search, (val) => {
  fetchCards(val);
    console.log(selected.value);

});

// watch(cards,


// )
const total = computed(() => {
  return cards.value.reduce((acc, curr) => acc + Number(curr.price || 0), 0);
});

watch(selected, (val) => {
  if (val) {
    const newItem = items.value.find(item => item.id === val);
    if (newItem) {
      const alreadyExists = cards.value.some(card => card.id === newItem.id);
      if (!alreadyExists) {
        cards.value.push(newItem);
      } else {
        console.log('Item already added — skipping duplicate');
      }
    }
  }
});

// The `onSearch` method is no longer needed


// ----------------------
// QR Scanner
// ----------------------
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
        await closeScanner();

        // Send QR to backend -> Inertia reload
        router.post(route('scan.qr.cards'), {
          qr_code: decodedText,
          is_sell_card: true,
        }, {
          // ⭐ The onSuccess callback is here ⭐
          onSuccess: (page) => {
            console.log(page);
            cards.value.push(page.props.scannedCards)
            // This code runs after the backend successfully processes the request
            // and the Inertia page has been reloaded.


            console.log('QR card scanned and added successfully!');
            // You can add a success notification here, for example:
            // showToast('Card added!');
          },
          onError: (errors) => {
            // This runs if there are validation errors from the server.
            console.error('Failed to add card:', errors);
            // showToast('Error adding card.');
          },
          onFinish: () => {
            // This runs regardless of success or failure.
            console.log('Finished processing QR code scan.');
          },
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
// Payment & Cancel
// ----------------------

const removeCard = (id) => {
  cards.value = cards.value.filter(card => card.id !== id)
}



const submitPayment = () => {

 form.cards = cards.value.map(c => c.id)

  form.post(route('sell-card.payment'), {
    onSuccess: () => {},
  });
};

const cancelPayment = () => {
  router.get(route('parkin.index'));
};

// ----------------------
// Lifecycle
// ----------------------
onBeforeUnmount(() => {
  closeScanner();
});
</script>

<style scoped>


:deep(.small-label .v-field-label),
:deep(.v-autocomplete__selection-text )

{
  font-size: 0.75rem !important;
}
.autocomplete-overlay .v-list-item-title {
  font-size: 0.75rem !important;
}

</style>

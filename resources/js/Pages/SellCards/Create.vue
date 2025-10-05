<template>
  <div class="payment-wrapper">
    <QrScanner v-model="isScanQR" :closeScanner="closeScanner" />

    <v-container fluid class="py-8">
      <v-row justify="center">
        <v-col cols="12" md="8" lg="6">
          <!-- Header Section -->
          <div class="page-header mb-6 text-center">
            <v-icon size="48" color="white" class="mb-2">mdi-cash-register</v-icon>
            <h1 class="text-h4 font-weight-bold text-white mb-2">
              Card Payment
            </h1>
            <p class="text-white-70">
              Scan or search for cards to process payment
            </p>
          </div>

          <!-- Main Payment Card -->
          <v-card class="payment-card elevation-12" rounded="xl">
            <div class="card-header pa-6 pb-4">
              <div class="d-flex align-center justify-space-between">
                <h3 class="text-h6 font-weight-bold text-indigo-darken-4">
                  <v-icon class="mr-2">mdi-credit-card-search</v-icon>
                  Select Cards
                </h3>
                <v-chip 
                  color="indigo-darken-4" 
                  variant="flat" 
                  size="small"
                >
                  {{ cards.length }} item{{ cards.length !== 1 ? 's' : '' }}
                </v-chip>
              </div>
            </div>

            <v-divider></v-divider>

            <div class="pa-6">
              <!-- Search Autocomplete -->
              <div class="search-section mb-4">
                <v-autocomplete
                  v-model="selected"
                  v-model:search="search"
                  :items="items"
                  item-title="card_number"
                  item-value="id"
                  label="Search Card Number"
                  density="comfortable"
                  :loading="loading"
                  hide-no-data
                  hide-details
                  variant="outlined"
                  bg-color="white"
                  prepend-inner-icon="mdi-magnify"
                  clearable
                  class="search-input"
                >
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props">
                      <template v-slot:prepend>
                        <v-avatar color="indigo-lighten-5" size="36">
                          <v-icon size="20" color="indigo-darken-4">mdi-card-account-details</v-icon>
                        </v-avatar>
                      </template>
                      <template v-slot:subtitle>
                        <span class="text-caption">{{ item.raw.card_name }}</span>
                      </template>
                    </v-list-item>
                  </template>
                </v-autocomplete>
              </div>

              <!-- OR Divider -->
              <div class="divider-section mb-4">
                <v-divider></v-divider>
                <span class="divider-text">OR</span>
                <v-divider></v-divider>
              </div>

              <!-- Scan QR Button -->
              <v-btn
                class="mb-6 scan-btn"
                color="indigo-darken-4"
                block
                size="x-large"
                variant="flat"
                @click="scanQR"
              >
                <v-icon class="mr-2" size="24">mdi-qrcode-scan</v-icon>
                <span class="text-h6 font-weight-bold">Scan QR Code</span>
              </v-btn>

              <!-- Scanned Cards List -->
              <div class="cards-list-section">
                <div class="d-flex align-center justify-space-between mb-3">
                  <h4 class="text-subtitle-1 font-weight-bold text-indigo-darken-4">
                    Selected Cards
                  </h4>
                  <v-btn
                    v-if="cards.length > 0"
                    size="small"
                    color="error"
                    variant="text"
                    @click="clearAllCards"
                  >
                    <v-icon start size="18">mdi-delete-sweep</v-icon>
                    Clear All
                  </v-btn>
                </div>

                <v-expand-transition>
                  <div v-if="cards.length" class="cards-container">
                    <v-card
                      v-for="card in cards"
                      :key="card.id"
                      class="card-item elevation-2 mb-3"
                      rounded="lg"
                    >
                      <div class="pa-4 d-flex align-center justify-space-between">
                        <!-- Left Side -->
                        <div class="d-flex align-center flex-grow-1">
                          <v-avatar color="indigo-lighten-5" size="48" class="mr-3">
                            <v-icon size="28" color="indigo-darken-4">mdi-qrcode</v-icon>
                          </v-avatar>
                          <div>
                            <div class="text-subtitle-2 font-weight-bold">
                              {{ card.card_number }}
                            </div>
                            <div class="text-caption text-medium-emphasis">
                              {{ card.card_name }}
                            </div>
                          </div>
                        </div>

                        <!-- Price -->
                        <div class="price-section mr-3">
                          <div class="text-h6 font-weight-bold text-indigo-darken-4">
                            ₱{{ Number(card.price).toLocaleString() }}
                          </div>
                        </div>

                        <!-- Remove Button -->
                        <v-btn
                          icon
                          size="small"
                          color="error"
                          variant="text"
                          @click="removeCard(card.id)"
                        >
                          <v-icon size="20">mdi-close-circle</v-icon>
                        </v-btn>
                      </div>
                    </v-card>
                  </div>
                </v-expand-transition>

                <!-- Empty State -->
                <v-card
                  v-if="!cards.length"
                  class="empty-state pa-8 text-center"
                  flat
                  color="grey-lighten-4"
                  rounded="lg"
                >
                  <v-icon size="64" color="grey-lighten-1" class="mb-3">
                    mdi-credit-card-off-outline
                  </v-icon>
                  <p class="text-body-2 text-medium-emphasis mb-0">
                    No cards selected yet
                  </p>
                  <p class="text-caption text-medium-emphasis">
                    Search or scan a QR code to add cards
                  </p>
                </v-card>
              </div>
            </div>

            <v-divider></v-divider>

            <!-- Total Section -->
            <div class="total-section pa-6 pb-4">
              <v-card class="total-card elevation-0" color="indigo-lighten-5" rounded="lg">
                <div class="pa-4 d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <v-icon size="32" color="indigo-darken-4" class="mr-3">
                      mdi-calculator
                    </v-icon>
                    <div>
                      <p class="text-caption text-medium-emphasis mb-0">Total Amount</p>
                      <p class="text-h5 font-weight-bold text-indigo-darken-4 mb-0">
                        ₱{{ total.toLocaleString() }}
                      </p>
                    </div>
                  </div>
                  <v-chip
                    color="success"
                    variant="flat"
                    size="small"
                    v-if="cards.length > 0"
                  >
                    <v-icon start size="12">mdi-check</v-icon>
                    Ready
                  </v-chip>
                </div>
              </v-card>
            </div>

            <!-- Payment Form -->
            <div class="payment-form-section pa-6 pt-0">
              <h4 class="text-subtitle-1 font-weight-bold text-indigo-darken-4 mb-4">
                Payment Details
              </h4>

              <v-text-field
                v-model="form.cash_amount"
                label="Cash Amount"
                type="number"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-cash"
                prefix="₱"
                hide-details="auto"
                :error-messages="form.errors.cash_amount"
                class="mb-4"
              >
                <template v-slot:append-inner>
                  <v-btn
                    size="small"
                    variant="text"
                    color="indigo-darken-4"
                    @click="form.cash_amount = total"
                  >
                    Exact
                  </v-btn>
                </template>
              </v-text-field>

              <v-text-field
                v-model="form.customer"
                label="Sold To (Customer Name)"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-account"
                hide-details="auto"
                :error-messages="form.errors.customer"
              />

              <!-- Change Display -->
              <v-expand-transition>
                <v-alert
                  v-if="form.cash_amount && form.cash_amount >= total && cards.length > 0"
                  type="success"
                  variant="tonal"
                  class="mt-4"
                  density="compact"
                >
                  <div class="d-flex align-center justify-space-between">
                    <span class="font-weight-medium">Change:</span>
                    <span class="text-h6 font-weight-bold">
                      ₱{{ (Number(form.cash_amount) - total).toLocaleString() }}
                    </span>
                  </div>
                </v-alert>
              </v-expand-transition>

              <!-- Insufficient Amount Warning -->
              <v-expand-transition>
                <v-alert
                  v-if="form.cash_amount && form.cash_amount < total"
                  type="warning"
                  variant="tonal"
                  class="mt-4"
                  density="compact"
                >
                  <div class="d-flex align-center justify-space-between">
                    <span class="font-weight-medium">Insufficient Amount:</span>
                    <span class="text-subtitle-2 font-weight-bold">
                      Need ₱{{ (total - Number(form.cash_amount)).toLocaleString() }} more
                    </span>
                  </div>
                </v-alert>
              </v-expand-transition>
            </div>

            <v-divider></v-divider>

            <!-- Action Buttons -->
            <div class="action-buttons pa-6">
              <v-row dense>
                <v-col cols="12" sm="6">
                  <v-btn
                    block
                    size="x-large"
                    color="indigo-darken-4"
                    variant="flat"
                    @click="submitPayment"
                    :disabled="payDisabled || form.processing"
                    :loading="form.processing"
                    class="pay-btn"
                  >
                    <v-icon start size="24">mdi-check-circle</v-icon>
                    <span class="text-h6 font-weight-bold">Process Payment</span>
                  </v-btn>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-btn
                    block
                    size="x-large"
                    color="grey-darken-2"
                    variant="outlined"
                    @click="cancelPayment"
                    :disabled="form.processing"
                    class="cancel-btn"
                  >
                    <v-icon start size="24">mdi-close-circle</v-icon>
                    <span class="text-h6 font-weight-bold">Cancel</span>
                  </v-btn>
                </v-col>
              </v-row>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { Html5Qrcode } from 'html5-qrcode';
import { route } from 'ziggy-js';
import QrScanner from '../../Components/QrScanner.vue';
import axios from 'axios';

// Props & Refs
const props = defineProps({
  cardTemplate: Array,
  scannedCards: Array
});

// Autocomplete Setup
const selected = ref(null);
const items = ref([]);
const cards = ref([]);
const loading = ref(false);
const search = ref('');
const isScanQR = ref(false);
const html5QrCode = ref(null);

const form = useForm({
  customer: '',
  cash_amount: null,
  cards: []
});

const payDisabled = computed(() => {
  return cards.value.length === 0 || !form.cash_amount || !form.customer || form.cash_amount < total.value;
});

// Debounce function
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

// Watch the search input
watch(search, (val) => {
  fetchCards(val);
});

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
        selected.value = null; // Reset selection
        search.value = ''; // Clear search
      }
    }
  }
});

// QR Scanner
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

        router.post(route('scan.qr.cards'), {
          qr_code: decodedText,
          is_sell_card: true,
        }, {
          onSuccess: (page) => {
            cards.value.push(page.props.scannedCards);
          },
          onError: (errors) => {
            console.error('Failed to add card:', errors);
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

// Payment & Cancel
const removeCard = (id) => {
  cards.value = cards.value.filter(card => card.id !== id);
};

const clearAllCards = () => {
  cards.value = [];
};

const submitPayment = () => {
  form.cards = cards.value.map(c => c.id);

  form.post(route('sell-card.payment'), {
    onSuccess: () => {
      cards.value = [];
      form.reset();
    },
  });
};

const cancelPayment = () => {
  router.get(route('parkin.index'));
};

// Lifecycle
onBeforeUnmount(() => {
  closeScanner();
});
</script>

<style scoped>
.payment-wrapper {
  background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
  min-height: 100vh;
}

.text-white-70 {
  color: rgba(255, 255, 255, 0.7);
}

/* Header */
.page-header {
  animation: fadeInDown 0.6s ease-out;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Payment Card */
.payment-card {
  background: white;
  overflow: hidden;
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.card-header {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.05) 0%, rgba(26, 35, 126, 0.02) 100%);
}

/* Search Input */
.search-input :deep(.v-field) {
  border: 2px solid #e0e0e0;
  transition: all 0.3s ease;
}

.search-input :deep(.v-field--focused) {
  border-color: #1a237e;
  box-shadow: 0 0 0 4px rgba(26, 35, 126, 0.1);
}

.search-input :deep(.v-field-label) {
  font-size: 0.95rem !important;
  background: white;
  padding: 0 4px;
}

.search-input :deep(.v-field--focused .v-field-label),
.search-input :deep(.v-field--active .v-field-label) {
  font-size: 0.75rem !important;
}

/* Divider Section */
.divider-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.divider-text {
  font-weight: 600;
  color: #9e9e9e;
  font-size: 0.875rem;
  padding: 0 0.5rem;
}

/* Scan Button */
.scan-btn {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%) !important;
  transition: all 0.3s ease;
}

.scan-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(26, 35, 126, 0.3) !important;
}

/* Cards List */
.cards-container {
  max-height: 400px;
  overflow-y: auto;
  padding-right: 8px;
}

.cards-container::-webkit-scrollbar {
  width: 6px;
}

.cards-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.cards-container::-webkit-scrollbar-thumb {
  background: #1a237e;
  border-radius: 10px;
}

.card-item {
  background: white;
  border: 1px solid rgba(0, 0, 0, 0.08);
  transition: all 0.2s ease;
}

.card-item:hover {
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

/* Empty State */
.empty-state {
  border: 2px dashed #e0e0e0;
}

/* Total Section */
.total-card {
  border: 2px solid #c5cae9;
}

/* Payment Form */
.payment-form-section :deep(.v-field) {
  border: 2px solid #e0e0e0;
  transition: all 0.3s ease;
}

.payment-form-section :deep(.v-field--focused) {
  border-color: #1a237e;
  box-shadow: 0 0 0 4px rgba(26, 35, 126, 0.1);
}

.payment-form-section :deep(.v-field-label) {
  font-size: 0.95rem !important;
  background: white;
  padding: 0 4px;
}

.payment-form-section :deep(.v-field--focused .v-field-label),
.payment-form-section :deep(.v-field--active .v-field-label) {
  font-size: 0.75rem !important;
}

/* Action Buttons */
.pay-btn {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%) !important;
  transition: all 0.3s ease;
}

.pay-btn:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(26, 35, 126, 0.4) !important;
}

.cancel-btn {
  border: 2px solid #616161;
  transition: all 0.3s ease;
}

.cancel-btn:hover {
  transform: translateY(-2px);
  background: #616161;
  color: white;
}

/* Responsive */
@media (max-width: 600px) {
  .page-header h1 {
    font-size: 1.75rem;
  }

  .pay-btn .text-h6,
  .cancel-btn .text-h6,
  .scan-btn .text-h6 {
    font-size: 1rem !important;
  }

  .cards-container {
    max-height: 300px;
  }
}

/* Animations */
:deep(.v-enter-active),
:deep(.v-leave-active) {
  transition: all 0.3s ease;
}

:deep(.v-enter-from) {
  opacity: 0;
  transform: translateY(-10px);
}

:deep(.v-leave-to) {
  opacity: 0;
  transform: translateY(10px);
}
</style>
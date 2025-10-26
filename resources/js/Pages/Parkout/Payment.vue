<template>
  <!-- QR Scanner Dialog -->
  <v-dialog v-model="isScanQR" max-width="600" persistent>
    <v-card rounded="lg">
      <v-card-title class="pa-6 pb-4">
        <div class="d-flex align-center">
          <v-icon size="28" color="indigo-darken-4" class="mr-3">mdi-qrcode-scan</v-icon>
          <span class="text-h6 font-weight-bold">Scan Card QR Code</span>
        </div>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text class="pa-6">
        <div id="reader" style="width:100%; height:400px; border-radius: 12px; overflow: hidden;"></div>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions class="pa-4">
        <v-spacer></v-spacer>
        <v-btn color="error" variant="outlined" @click="closeScanner">
          <v-icon start>mdi-close</v-icon>
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Main Payment Page -->
  <div class="payment-wrapper">
    <v-container fluid class="py-8">
      <v-row justify="center">
        <v-col cols="12" md="10" lg="8">
          <!-- Header -->
          <div class="page-header mb-6 text-center">
            <h1 class="text-h4 font-weight-bold text-white mb-2">
              Payment Processing
            </h1>
          </div>

          <v-row>
            <!-- Left Side - Ticket Information -->
            <v-col cols="12" md="5">
              <v-card class="ticket-card elevation-8" rounded="sm">
                <div class="card-header pa-4">
                  <div class="d-flex align-center justify-space-between">
                    <h3 class="text-sm font-weight-bold text-indigo-darken-4">
                      Ticket Details
                    </h3>
                  </div>
                </div>

                <v-divider></v-divider>

                <div class="pa-6">
                  <!-- Ticket Number -->
                  <div class="info-item mb-4">
                    <div class="d-flex align-center justify-space-between mb-2">
                      <span class="text-caption text-indigo-darken-4">Ticket Number</span>
                      <p class="text-xs font-weight-bold text-indigo-darken-4 mb-0">
                        {{ ticket.data.ticket_no }}
                      </p>
                    </div>
                  </div>

                  <v-divider class="my-4"></v-divider>

                  <!-- Park In -->
                  <div class="info-item mb-4">
                    <div class="d-flex align-center justify-space-between text-indigo-darken-4 mb-2">
                      <span class="text-caption">Park In</span>
                      <div class="d-flex flex-column align-end">
                        <span class="text-xs font-weight-bold">
                          {{ formatDate(ticket.data.park_datetime) }}
                        </span>
                        <span class="text-xs font-weight-bold">
                          {{ parkinTime }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Park Out -->
                  <div class="info-item mb-4">
                    <div class="d-flex align-center justify-space-between mb-2 text-indigo-darken-4">
                      <span class="text-caption">Park Out</span>
                      <div class="d-flex flex-column align-end">
                        <span class="text-xs font-weight-bold">
                          {{ formatDate(ticket.data.park_out_datetime) }}
                        </span>
                        <span class="text-xs font-weight-bold">
                          {{ parkoutTime }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <v-divider class="my-4"></v-divider>

                  <!-- Number of Days -->
                  <div class="info-item mb-4" v-if="ticket.data.days_parked > 0">
                    <div class="d-flex align-center justify-space-between">
                      <span class="text-caption text-indigo-darken-4">Number of Days</span>
                      <span class="text-h6 font-weight-bold text-indigo-darken-4">
                        {{ ticket.data.days_parked }}
                      </span>
                    </div>
                  </div>

                  <!-- Number of Hours -->
                  <div class="info-item mb-4" v-if="hoursPark > 0">
                    <div class="d-flex align-center justify-space-between">
                      <span class="text-caption text-indigo-darken-4">Number of Hours</span>
                      <span class="text-sm font-weight-bold text-indigo-darken-4">
                        {{ hoursPark }}
                      </span>
                    </div>
                  </div>

                  <v-divider class="my-4"></v-divider>

                  <!-- Total Bill -->
                  <v-card class="total-card elevation-0" color="indigo-lighten-5" rounded="sm">
                    <div class="pa-4">
                      <div class="d-flex align-center justify-space-between">
                        <div>
                          <p class="text-caption text-medium-emphasis mb-1">Total Parking Fee</p>
                          <p class="text-md font-weight-bold text-indigo-darken-4 mb-0">
                            {{ formatCurrency(ticket.data.park_fee) }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </v-card>
                </div>
              </v-card>
            </v-col>

            <!-- Right Side - Payment Form -->
            <v-col cols="12" md="7">
              <v-card class="payment-card elevation-8" rounded="sm">
                <div class="card-header pa-4">
                  <div class="d-flex align-center justify-space-between">
                    <h3 class="text-sm font-weight-bold text-indigo-darken-4">
                      Payment Method
                    </h3>
                  </div>
                </div>

                <v-divider></v-divider>


                <div class="pa-6">

<!-- Card Number Input + Search -->
<div class="d-flex align-center mb-4">
  <v-text-field
    v-model.trim="card_number"
    label="Card Number"
    variant="outlined"
    type="text"
    density="compact"
    hide-details="auto"
    clearable
    placeholder="Enter or scan card number"
    class="flex-grow-1 mr-3"
    @keyup.enter="searchCard"
  >
    <template v-slot:prepend-inner>
      <v-icon color="indigo-darken-4">mdi-card-account-details-outline</v-icon>
    </template>
  </v-text-field>

    <v-btn
        color="indigo-darken-4"
        variant="flat"
        @click="searchCard"
        :disabled="!card_number"
        
    >
        <v-icon start size="20">mdi-magnify</v-icon>
        Search
    </v-btn>
    </div>

    <!-- Scan Card Button -->
    <v-btn
    color="indigo-darken-4"
    size="large"
    block
    @click="scanQR"
    class="my-6 scan-card-btn text-xs"
    style="text-transform: none;"
    >
    <v-icon start size="24">mdi-qrcode-scan</v-icon>
    Scan Card
    </v-btn>

                                <!-- Scanned Cards Table -->
                  <div v-if="items.length > 0" class="cards-section mb-6">
                    <h4 class="text-sm font-weight-bold text-indigo-darken-4 mb-3">
                      Scanned Cards
                    </h4>

                    <v-data-table-server
                      :headers="headers"
                      :items="items"
                      :items-per-page="5"
                      density="comfortable"
                      class="cards-table elevation-2"
                      disable-sort
                      hide-default-footer
                    >
                      <template v-slot:no-data>
                        <div class="text-center py-8">
                          <v-icon size="48" color="grey-lighten-1" class="mb-3">
                            mdi-credit-card-off-outline
                          </v-icon>
                          <p class="text-sm text-medium-emphasis">
                            No cards scanned yet
                          </p>
                        </div>
                      </template>
                    </v-data-table-server>
                  </div>

                  <!-- Summary Section -->
                  <v-card v-if="items.length > 0" class="summary-card elevation-0 mb-6" color="grey-lighten-4" rounded="lg">
                    <div class="pa-4">
                      <div class="d-flex align-center justify-space-between mb-3">
                        <span class="text-sm font-weight-medium">Covered by Cards:</span>
                        <span class="text-lg font-weight-bold text-success">
                          {{ formatCurrency(totalCovered) }}
                        </span>
                      </div>
                      <v-divider class="my-3"></v-divider>
                      <div class="d-flex align-center justify-space-between">
                        <span class="text-sm font-weight-bold text-indigo-darken-4">
                          Amount Due:
                        </span>
                        <span class="text-lg font-weight-bold text-error">
                          {{ formatCurrency(cashNeeded) }}
                        </span>
                      </div>
                    </div>
                  </v-card>

                  <!-- Payment Method Selection -->
                  <div class="payment-method-section mb-6">
                    <h4 class="text-sm font-weight-bold text-indigo-darken-4 mb-3">
                      Select Payment Method
                    </h4>

                    <v-card class="payment-options elevation-0" rounded="lg" variant="outlined">
                      <v-radio-group v-model="paymentMethod" :disabled="disAbledPayment" hide-details density="compact">
                        <v-radio value="cash" class="payment-option-radio" density="compact">
                          <template v-slot:label>
                            <div class="d-flex align-center pa-2 w-100">
                              <v-avatar color="green-lighten-4" size="28" class="mr-3">
                                <v-icon color="green-darken-2" size="16">mdi-cash</v-icon>
                              </v-avatar>
                              <div>
                                <p class="text-xs font-weight-bold mb-0">Cash Payment</p>
                                <p class="text-xs text-medium-emphasis mb-0">Pay with physical cash</p>
                              </div>
                            </div>
                          </template>
                        </v-radio>
                        
                        <v-divider></v-divider>
                        
                        <v-radio value="gcash" class="payment-option-radio" density="compact">
                          <template v-slot:label>
                            <div class="d-flex align-center pa-2 w-100">
                              <v-avatar color="blue-lighten-4" size="28" class="mr-3">
                                <v-icon color="blue-darken-2" size="16">mdi-cellphone</v-icon>
                              </v-avatar>
                              <div>
                                <p class="text-xs font-weight-bold mb-0">GCash</p>
                                <p class="text-xs text-medium-emphasis mb-0">Pay via GCash mobile wallet</p>
                              </div>
                            </div>
                          </template>
                        </v-radio>
                      </v-radio-group>
                    </v-card>
                  </div>

                  <!-- Cash Payment Section -->
                  <v-expand-transition>
                    <div v-if="paymentMethod === 'cash'" class="cash-section">
                      <h4 class="text-sm font-weight-bold text-indigo-darken-4 mb-3">
                        Cash Amount
                      </h4>

                      <v-text-field
                        v-model.number="cashAmount"
                        variant="outlined"
                        type="number"
                        :disabled="disAbledPayment"
                        prefix="₱"
                        density="comfortable"
                        hide-details="auto"
                        :hint="disAbledPayment ? 'Payment fully covered by cards' : ''"
                        persistent-hint
                        min="0"
                        step="0.01"
                      >
                        <template v-slot:prepend-inner>
                          <v-icon color="green-darken-2">mdi-cash</v-icon>
                        </template>
                      </v-text-field>

                      <!-- Change Display -->
                      <v-expand-transition>
                        <v-alert
                          v-if="cashAmount && cashAmount >= cashNeeded"
                          type="success"
                          variant="tonal"
                          class="mt-4"
                          density="compact"
                        >
                          <div class="d-flex align-center justify-space-between">
                            <span class="font-weight-medium">Change:</span>
                            <span class="text-sm font-weight-bold">
                              {{ formatCurrency(cashAmount - cashNeeded) }}
                            </span>
                          </div>
                        </v-alert>
                      </v-expand-transition>

                      <!-- Insufficient Amount Warning -->
                      <v-expand-transition>
                        <v-alert
                          v-if="cashAmount && cashAmount < cashNeeded"
                          type="warning"
                          variant="tonal"
                          class="mt-4"
                          density="compact"
                        >
                          <div class="d-flex align-center justify-space-between">
                            <span class="font-weight-medium">Insufficient Amount:</span>
                            <span class="text-subtitle-2 font-weight-bold">
                              Need {{ formatCurrency(cashNeeded - cashAmount) }} more
                            </span>
                          </div>
                        </v-alert>
                      </v-expand-transition>
                    </div>
                  </v-expand-transition>

                  <!-- GCash Payment Section -->
                  <v-expand-transition>
                    <div v-if="paymentMethod === 'gcash'" class="gcash-section">
                      <h4 class="text-sm font-weight-bold text-indigo-darken-4 mb-3">
                        GCash Details
                      </h4>

                      <v-text-field
                        v-model.number="gcash_amount"
                        label="GCash Amount Paid"
                        variant="outlined"
                        type="number"
                        :disabled="disAbledPayment"
                        prefix="₱"
                        density="comfortable"
                        hide-details="auto"
                        placeholder="0.00"
                        min="0"
                        step="0.01"
                      >
                        <template v-slot:prepend-inner>
                          <v-icon color="blue-darken-2">mdi-cash</v-icon>
                        </template>
                      </v-text-field>

                      <v-text-field
                        v-model="gcashReferenceNumber"
                        label="Reference Number"
                        variant="outlined"
                        type="text"
                        :disabled="disAbledPayment"
                        density="comfortable"
                        hide-details="auto"
                        class="mt-4"
                        placeholder="Enter GCash reference number"
                      >
                        <template v-slot:prepend-inner>
                          <v-icon size="small" color="blue-darken-2">mdi-pound</v-icon>
                        </template>
                      </v-text-field>

                      <!-- Change Display for GCash -->
                      <v-expand-transition>
                        <v-alert
                          v-if="gcash_amount && gcash_amount >= cashNeeded"
                          type="success"
                          variant="tonal"
                          class="mt-4"
                          density="compact"
                        >
                          <div class="d-flex align-center justify-space-between">
                            <span class="text-sm font-weight-medium">Change:</span>
                            <span class="text-sm font-weight-bold">
                              {{ formatCurrency(gcash_amount - cashNeeded) }}
                            </span>
                          </div>
                        </v-alert>
                      </v-expand-transition>

                      <!-- Insufficient Amount Warning for GCash -->
                      <v-expand-transition>
                        <v-alert
                          v-if="gcash_amount && gcash_amount < cashNeeded"
                          type="warning"
                          variant="tonal"
                          class="mt-4"
                          density="compact"
                        >
                          <div class="d-flex align-center justify-space-between">
                            <span class="font-weight-medium">Insufficient Amount:</span>
                            <span class="text-subtitle-2 font-weight-bold">
                              Need {{ formatCurrency(cashNeeded - gcash_amount) }} more
                            </span>
                          </div>
                        </v-alert>
                      </v-expand-transition>

                      <v-alert
                        type="info"
                        variant="tonal"
                        class="mt-4"
                        density="compact"
                      >
                        <div class="text-sm">
                          <strong>Amount to pay:</strong> {{ formatCurrency(cashNeeded) }}
                        </div>
                      </v-alert>
                    </div>
                  </v-expand-transition>
                </div>

                <v-divider></v-divider>

                <!-- Action Button -->
                <div class="pa-4">
                  <v-btn
                    :disabled="!isPaymentValid"
                    :loading="isSubmitting"
                    block
                    size="large"
                    @click="submitPayment"
                    color="indigo-darken-4"
                    class="pay-button"
                  >
                    <span class="text-sm">Complete Payment</span>
                  </v-btn>
                </div>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { computed, ref, onBeforeUnmount } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { Html5Qrcode } from 'html5-qrcode'
import dayjs from 'dayjs'
import { formatCurrency } from '../../utils/utility'

const props = defineProps({
  ticket: Object,
  scannedCards: Array,
  totalCovered: Number,
  cashNeeded: Number,
})

const isScanQR = ref(false)
const html5QrCode = ref(null)
const paymentMethod = ref('cash')
const cashAmount = ref(null)
const gcash_amount = ref(null)
const gcashReferenceNumber = ref('')
const isSubmitting = ref(false)
const card_number = ref('')


const scannedCards = computed(() => props.scannedCards || [])
const totalCovered = computed(() => props.totalCovered || 0)
const cashNeeded = computed(() => props.cashNeeded || 0)

const hoursPark = computed(() => {
  return props.ticket.data.hours_parked
})

const parkinTime = computed(() => {
  return props.ticket.data.park_datetime
    ? dayjs(props.ticket.data.park_datetime).format("hh:mm A")
    : null
})

const parkoutTime = computed(() => {
  return props.ticket.data.park_out_datetime
    ? dayjs(props.ticket.data.park_out_datetime).format("hh:mm A")
    : null
})

const disAbledPayment = computed(() => {
  return totalCovered.value === props.ticket.data.park_fee
})

const isPaymentValid = computed(() => {
  // If payment is fully covered by cards
  if (disAbledPayment.value) {
    return scannedCards.value.length > 0
  }


  // If using cash
  if (paymentMethod.value === 'cash') {
    return cashAmount.value && cashAmount.value >= cashNeeded.value
  }

  // If using GCash - check amount and reference number
  if (paymentMethod.value === 'gcash') {
    return gcash_amount.value && 
           gcash_amount.value >= cashNeeded.value &&
           gcashReferenceNumber.value.trim() !== ''
  }

  return false
})



const searchCard = async () => {
  if (!card_number.value) return

  try {
    isSubmitting.value = true
    await router.post(route('scan.qr.cards'), {
      qr_code: card_number.value, // reuse same backend route
      ticket_id: props.ticket.data.id,
      ticket_uuid: props.ticket.data.uuid,
    })
  } catch (err) {
    console.error('Card search failed:', err)
  } finally {
    isSubmitting.value = false
  }
}

const headers = [
  { key: 'card_number', title: 'Card Number' },
  { key: 'price', title: 'Price' },
  { key: 'balance', title: 'Balance' },
]

const items = computed(() => {
  return scannedCards.value.map(item => ({
    card_number: item.card_number,
    no_of_days: item.no_of_days,
    price: formatCurrency(item.price),
    balance: formatCurrency(item.balance),
  }))
})

const formatDate = (date) => {
  return date ? dayjs(date).format('MM/DD/YYYY') : 'N/A'
}

const submitPayment = () => {
  if (isSubmitting.value) return
  
  isSubmitting.value = true
  
  const form = useForm({
    ticket_id: props.ticket.data.id,
    hours_parked: props.ticket.data.hours_parked,
    days_parked: props.ticket.data.days_parked,
    payment_method: paymentMethod.value,
    cash_amount: paymentMethod.value === 'cash' ? cashAmount.value : null,
    gcash_amount: paymentMethod.value === 'gcash' ? gcash_amount.value : null,
    gcash_reference: paymentMethod.value === 'gcash' ? gcashReferenceNumber.value : null,
    cards: scannedCards.value.map(c => c.id),
  })

  form.post(route('store.payment'), {
    onSuccess: () => {
      isSubmitting.value = false
    },
    onError: () => {
      isSubmitting.value = false
    },
  })
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

onBeforeUnmount(() => {
  closeScanner()
})
</script>

<style scoped>
.payment-wrapper {
  background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
  min-height: 100vh;
}

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

.ticket-card,
.payment-card {
  background: white;
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

.info-item {
  transition: all 0.2s ease;
}

.total-card {
  border: 2px solid #c5cae9;
}

.scan-card-btn {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%) !important;
  transition: all 0.3s ease;
}

.scan-card-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(26, 35, 126, 0.3);
}

.cards-table :deep(thead) {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
}

.cards-table :deep(thead th) {
  color: white !important;
  font-weight: 600 !important;
}

.summary-card {
  border-left: 4px solid #1a237e;
}

.payment-options {
  border: 2px solid #e0e0e0;
  transition: all 0.3s ease;
}

.payment-option-radio :deep(.v-label) {
  width: 100%;
  opacity: 1 !important;
}

.payment-option-radio :deep(.v-selection-control__wrapper) {
  margin-right: 8px;
  transform: scale(0.85);
}

.cash-section :deep(.v-field),
.gcash-section :deep(.v-field) {
  border: 2px solid #e0e0e0;
  transition: all 0.3s ease;
}

.cash-section :deep(.v-field--focused),
.gcash-section :deep(.v-field--focused) {
  border-color: #1a237e;
  box-shadow: 0 0 0 4px rgba(26, 35, 126, 0.1);
}

.pay-button {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%) !important;
  transition: all 0.3s ease;
}

.pay-button:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(26, 35, 126, 0.4) !important;
}

@media (max-width: 960px) {
  .page-header h1 {
    font-size: 1.75rem;
  }
}
</style>
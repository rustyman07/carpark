<template>
  <!-- QR Scanner Dialog -->
  <v-dialog v-model="isScanQR" max-width="600">
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
            <v-icon size="48" color="white" class="mb-2">mdi-cash-register</v-icon>
            <h1 class="text-h4 font-weight-bold text-white mb-2">
              Payment Processing
            </h1>
            <p class="text-white-70">
              Complete your parking payment
            </p>
          </div>

          <v-row>
            <!-- Left Side - Ticket Information -->
            <v-col cols="12" md="5">
              <v-card class="ticket-card elevation-8" rounded="xl">
                <div class="card-header pa-6 pb-4">
                  <div class="d-flex align-center justify-space-between">
                    <h3 class="text-h6 font-weight-bold text-indigo-darken-4">
                      <v-icon class="mr-2">mdi-ticket-confirmation</v-icon>
                      Ticket Details
                    </h3>
                    <v-chip color="success" variant="flat" size="small">
                      Active
                    </v-chip>
                  </div>
                </div>

                <v-divider></v-divider>

                <div class="pa-6">
                  <!-- Ticket Info Items -->
                  <div class="info-item mb-4">
                    <div class="d-flex align-center mb-2">
                      <v-icon size="20" color="indigo-darken-4" class="mr-2">mdi-barcode</v-icon>
                      <span class="text-caption text-medium-emphasis">Ticket Number</span>
                    </div>
                    <p class="text-h6 font-weight-bold text-indigo-darken-4 mb-0 ml-7">
                      {{ ticket.data.ticket_no }}
                    </p>
                  </div>

                  <v-divider class="my-4"></v-divider>

                  <div class="info-item mb-4">
                    <div class="d-flex align-center mb-2">
                      <v-icon size="20" color="success" class="mr-2">mdi-login</v-icon>
                      <span class="text-caption text-medium-emphasis">Park In</span>
                    </div>
                    <p class="text-subtitle-1 font-weight-medium mb-0 ml-7">
                      {{ formatDate(ticket.data.park_datetime) }}
                    </p>
                    <p class="text-body-2 text-success font-weight-bold ml-7">
                      {{ parkinTime }}
                    </p>
                  </div>

                  <div class="info-item mb-4">
                    <div class="d-flex align-center mb-2">
                      <v-icon size="20" color="error" class="mr-2">mdi-logout</v-icon>
                      <span class="text-caption text-medium-emphasis">Park Out</span>
                    </div>
                    <p class="text-subtitle-1 font-weight-medium mb-0 ml-7">
                      {{ formatDate(ticket.data.park_out_datetime) }}
                    </p>
                    <p class="text-body-2 text-error font-weight-bold ml-7">
                      {{ parkoutTime }}
                    </p>
                  </div>

                  <v-divider class="my-4"></v-divider>

                  <div class="info-item mb-4" v-if="ticket.data.days_parked !== 0">
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <v-icon size="20" color="indigo-darken-4" class="mr-2">mdi-calendar-range</v-icon>
                        <span class="text-body-2">Number of Days</span>
                      </div>
                      <span class="text-h6 font-weight-bold text-indigo-darken-4">
                        {{ ticket.data.days_parked }}
                      </span>
                    </div>
                  </div>

                  <div class="info-item mb-4" v-if="hoursPark !== 0">
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <v-icon size="20" color="indigo-darken-4" class="mr-2">mdi-clock-outline</v-icon>
                        <span class="text-body-2">Number of Hours</span>
                      </div>
                      <span class="text-h6 font-weight-bold text-indigo-darken-4">
                        {{ hoursPark }}
                      </span>
                    </div>
                  </div>

                  <v-divider class="my-4"></v-divider>

                  <!-- Total Bill -->
                  <v-card class="total-card elevation-0" color="indigo-lighten-5" rounded="lg">
                    <div class="pa-4">
                      <div class="d-flex align-center justify-space-between">
                        <div>
                          <p class="text-caption text-medium-emphasis mb-1">Total Parking Fee</p>
                          <p class="text-h5 font-weight-bold text-indigo-darken-4 mb-0">
                            {{ formatCurrency(ticket.data.park_fee) }}
                          </p>
                        </div>
                        <v-icon size="40" color="indigo-darken-4">mdi-cash-multiple</v-icon>
                      </div>
                    </div>
                  </v-card>
                </div>
              </v-card>
            </v-col>

            <!-- Right Side - Payment Form -->
            <v-col cols="12" md="7">
              <v-card class="payment-card elevation-8" rounded="xl">
                <div class="card-header pa-6 pb-4">
                  <div class="d-flex align-center justify-space-between">
                    <h3 class="text-h6 font-weight-bold text-indigo-darken-4">
                      <v-icon class="mr-2">mdi-credit-card</v-icon>
                      Payment Method
                    </h3>
                  </div>
                </div>

                <v-divider></v-divider>

                <div class="pa-6">
                  <!-- Scan Card Button -->
                  <v-btn
                    color="indigo-darken-4"
                    size="large"
                    block
                    @click="scanQR"
                    class="mb-6 scan-card-btn"
                  >
                    <v-icon start size="24">mdi-qrcode-scan</v-icon>
                    Scan Card QR Code
                  </v-btn>

                  <!-- Scanned Cards Table -->
                  <div class="cards-section mb-6">
                    <h4 class="text-subtitle-1 font-weight-bold text-indigo-darken-4 mb-3">
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
                          <p class="text-body-2 text-medium-emphasis">
                            No cards scanned yet
                          </p>
                        </div>
                      </template>
                    </v-data-table-server>
                  </div>

                  <!-- Summary Section -->
                  <v-card class="summary-card elevation-0 mb-6" color="grey-lighten-4" rounded="lg">
                    <div class="pa-4">
                      <div class="d-flex align-center justify-space-between mb-3">
                        <span class="text-body-1 font-weight-medium">Covered by Cards:</span>
                        <span class="text-h6 font-weight-bold text-success">
                          {{ formatCurrency(totalCovered) }}
                        </span>
                      </div>
                      <v-divider class="my-3"></v-divider>
                      <div class="d-flex align-center justify-space-between">
                        <span class="text-h6 font-weight-bold text-indigo-darken-4">
                          Amount Due:
                        </span>
                        <span class="text-h5 font-weight-bold text-error">
                          {{ formatCurrency(cashNeeded) }}
                        </span>
                      </div>
                    </div>
                  </v-card>

                  <!-- Cash Payment -->
                  <div class="cash-section">
                    <h4 class="text-subtitle-1 font-weight-bold text-indigo-darken-4 mb-3">
                      <v-icon class="mr-2">mdi-cash</v-icon>
                      Cash Payment
                    </h4>

                    <v-text-field
                      v-model="cashAmount"
                      label="Cash Amount"
                      variant="outlined"
                      type="number"
                      :disabled="disAbledPayment"
                      prefix="₱"
                      density="comfortable"
                      hide-details="auto"
                      :hint="disAbledPayment ? 'Payment fully covered by cards' : ''"
                      persistent-hint
                    >
                      <template v-slot:prepend-inner>
                        <v-icon color="indigo-darken-4">mdi-cash</v-icon>
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
                          <span class="text-h6 font-weight-bold">
                            {{ formatCurrency(Number(cashAmount) - cashNeeded) }}
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
                            Need {{ formatCurrency(cashNeeded - Number(cashAmount)) }} more
                          </span>
                        </div>
                      </v-alert>
                    </v-expand-transition>
                  </div>
                </div>

                <v-divider></v-divider>

                <!-- Action Button -->
                <div class="pa-6">
                  <v-btn
                    :disabled="scannedCards.length === 0 && !cashAmount"
                    block
                    size="x-large"
                    @click="submitPayment"
                    color="indigo-darken-4"
                    class="pay-button"
                  >
                    <v-icon start size="28">mdi-check-circle</v-icon>
                    <span class="text-h6 font-weight-bold">Complete Payment</span>
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
const cashAmount = ref(null)
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
  if (totalCovered.value === props.ticket.data.park_fee) {
    return true
  }
  else return false
})

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
  const form = useForm({
    ticket_id: props.ticket.data.id,
    hours_parked: props.ticket.data.hours_parked,
    days_parked: props.ticket.data.days_parked,
    cash_amount: cashAmount.value,
    cards: scannedCards.value.map(c => c.id),
  })

  form.post(route('store.payment'), {
    onSuccess: () => {
      // Success handling
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

.text-white-70 {
  color: rgba(255, 255, 255, 0.7);
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

.cash-section :deep(.v-field) {
  border: 2px solid #e0e0e0;
  transition: all 0.3s ease;
}

.cash-section :deep(.v-field--focused) {
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
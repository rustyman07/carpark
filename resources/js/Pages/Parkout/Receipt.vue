<template>
  <div class="receipt-wrapper">
    <v-container fluid class="py-8">
      <v-row justify="center">
        <v-col cols="12" md="8" lg="6">
          <!-- Success Header -->
          <div class="success-header mb-6 text-center">
            <v-avatar color="success" size="80" class="mb-4 success-avatar">
              <v-icon size="50" color="white">mdi-check-circle</v-icon>
            </v-avatar>
            <h1  color="success" class="text-h4 font-weight-bold text-white mb-2">
              Payment Successful!
            </h1>
            <!-- <p class="text-white-70 text-h6">
              Thank you for parking with us
            </p> -->
          </div>

          <!-- Receipt Card -->
          <v-card ref="receiptContent" class="receipt-card elevation-2" rounded="sm">
            <!-- Company Header -->
            <!-- <div class="company-header pa-6 text-center">
              <v-icon size="48" color="white" class="mb-3">mdi-parking</v-icon>
              <h2 class="text-h6 font-weight-bold text-white mb-1">
                {{ company.name }}
              </h2>
              <p class="text-body-2 text-white mb-1">{{ company.address }}</p>
              <p class="text-body-2 text-white">{{ company.contact }}</p>
            </div> -->

            <v-divider></v-divider>

            <!-- Receipt Body -->
            <div class="receipt-body pa-6">
              <!-- Ticket Information Section -->
              <div class="section mb-6">
                <h3 class="section-title mb-4">
                  <v-icon size="20" class="mr-2">mdi-information-outline</v-icon>
                  Parking Details
                </h3>

                <v-card class="info-card elevation-0" color="grey-lighten-5" rounded="lg">
                  <div class="pa-4">
                    <div class="info-row mb-3">
                      <div class="d-flex align-center">
                        <v-icon size="18" color="indigo-darken-4" class="mr-2">mdi-barcode</v-icon>
                        <span class="label">Ticket No.</span>
                      </div>
                      <span class="value">{{ props.ticket.ticket_no }}</span>
                    </div>

                    <div class="info-row mb-3">
                      <div class="d-flex align-center">
                        <v-icon size="18" color="indigo-darken-4" class="mr-2">mdi-car</v-icon>
                        <span class="label">Plate No.</span>
                      </div>
                      <span class="value">{{ props.ticket.plate_no }}</span>
                    </div>

                    <v-divider class="my-3"></v-divider>

                    <div class="info-row mb-3">
                      <div class="d-flex align-center">
                        <v-icon size="18" color="success" class="mr-2">mdi-login</v-icon>
                        <span class="label">From</span>
                      </div>
                      <div class="text-right">
                        <div class="value">{{ parkinDate }}</div>
                        <div class="time-badge success">{{ parkinTime }}</div>
                      </div>
                    </div>

                    <div class="info-row mb-3">
                      <div class="d-flex align-center">
                        <v-icon size="18" color="error" class="mr-2">mdi-logout</v-icon>
                        <span class="label">To</span>
                      </div>
                      <div class="text-right">
                        <div class="value">{{ parkoutDate }}</div>
                        <div class="time-badge error">{{ parkoutTime }}</div>
                      </div>
                    </div>

                    <v-divider class="my-3"></v-divider>

                    <div class="info-row">
                      <div class="d-flex align-center">
                        <v-icon size="18" color="indigo-darken-4" class="mr-2">mdi-clock-outline</v-icon>
                        <span class="label">Duration</span>
                      </div>
                      <span class="value font-weight-bold">{{ duration }}</span>
                    </div>
                  </div>
                </v-card>
              </div>

              <!-- Payment Breakdown Section -->
              <div class="section mb-6">
                <h3 class="section-title mb-4">
                  <v-icon size="20" class="mr-2">mdi-receipt-text</v-icon>
                  Payment Breakdown
                </h3>

                <v-card class="payment-card elevation-0" color="grey-lighten-5" rounded="lg">
                  <div class="pa-4">
                    <div class="info-row mb-3">
                      <span class="label">Parking Fee</span>
                      <span class="value">{{ formatCurrency(props.ticket.park_fee) }}</span>
                    </div>

                    <!-- Cards Used -->
                    <div v-if="filteredCard.length" class="cards-section my-4">
                      <v-divider class="mb-3"></v-divider>
                      <p class="text-caption text-medium-emphasis mb-3">Cards Used:</p>
                      <v-card
                        v-for="card in filteredCard"
                        :key="card.id"
                        class="card-item elevation-1 mb-2"
                        rounded="lg"
                      >
                        <div class="pa-3">
                          <div class="d-flex align-center justify-space-between mb-2">
                            <div class="d-flex align-center">
                              <v-icon size="20" color="indigo-darken-4" class="mr-2">mdi-credit-card</v-icon>
                              <span class="text-body-2 font-weight-medium">{{ card.card_number }}</span>
                            </div>
                            <v-chip color="success" variant="flat" size="x-small">Used</v-chip>
                          </div>
                          <div class="d-flex justify-space-between">
                            <span class="text-caption text-medium-emphasis">Remaining Balance:</span>
                            <span class="text-caption font-weight-bold">{{ formatCurrency(card.balance) }}</span>
                          </div>
                        </div>
                      </v-card>
                    </div>

                    <!-- Cash Payment -->
                    <div v-if="filteredCash.length" class="cash-section my-4">
                      <v-divider class="mb-3"></v-divider>
                      <div v-for="item in filteredCash" :key="item.id" class="info-row mb-2">
                        <div class="d-flex align-center">
                          <v-icon size="18" color="success" class="mr-2">mdi-cash</v-icon>
                          <span class="label">Paid in Cash</span>
                        </div>
                        <span class="value">{{ formatCurrency(item.amount) }}</span>
                      </div>
                    </div>

                    <v-divider class="my-3"></v-divider>

                    <!-- Amount if applicable -->
                    <div v-if="props.payment.amount !== 0" class="info-row mb-3">
                      <span class="label">Amount Received</span>
                      <span class="value">{{ formatCurrency(props.payment.amount) }}</span>
                    </div>

                    <!-- Total Amount -->
                    <div class="info-row mb-3">
                      <span class="label font-weight-bold">Total Amount</span>
                      <span class="value font-weight-bold text-indigo-darken-4">
                        {{ formatCurrency(props.payment.total_amount) }}
                      </span>
                    </div>

                    <v-divider class="my-3"></v-divider>

                    <!-- Change -->
                    <v-card class="change-card elevation-0" color="success-lighten-5" rounded="lg">
                      <div class="pa-3">
                        <div class="d-flex align-center justify-space-between">
                          <div class="d-flex align-center">
                            <v-icon color="success" class="mr-2">mdi-cash-refund</v-icon>
                            <span class="font-weight-bold text-success">Change</span>
                          </div>
                          <span class="text-h6 font-weight-bold text-success">
                            {{ formatCurrency(props.payment.change) }}
                          </span>
                        </div>
                      </div>
                    </v-card>
                  </div>
                </v-card>
              </div>

              <!-- Thank You Message -->
              <v-card class="thank-you-card elevation-0" color="indigo-lighten-5" rounded="lg">
                <div class="pa-4 text-center">
                  <v-icon size="32" color="indigo-darken-4" class="mb-2">mdi-heart</v-icon>
                  <p class="text-body-1 font-weight-medium text-indigo-darken-4 mb-1">
                    Thank you for choosing us!
                  </p>
                  <p class="text-caption text-medium-emphasis mb-0">
                    Drive safely and have a great day
                  </p>
                </div>
              </v-card>
            </div>

            <v-divider></v-divider>

            <!-- Footer -->
            <div class="receipt-footer pa-4 text-center">
              <p class="text-caption text-medium-emphasis mb-0">
                Receipt generated on {{ new Date().toLocaleString() }}
              </p>
            </div>
          </v-card>

          <!-- Action Buttons -->
          <div class="action-buttons mt-6">
            <v-row>
              <v-col cols="12" sm="6">
                <v-btn
                  color="white"
                  size="x-large"
                  block
                  variant="flat"
                  @click="printReceipt"
                >
                  <v-icon start>mdi-printer</v-icon>
                  Print Receipt
                </v-btn>
              </v-col>
              <v-col cols="12" sm="6">
                <v-btn
                  color="indigo-darken-4"
                  size="x-large"
                  block
                  variant="flat"
                  @click="goToDashboard"
                >
                  <v-icon start>mdi-home</v-icon>
                  Back to Dashboard
                </v-btn>
              </v-col>
            </v-row>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed } from "vue"
import html2pdf from "html2pdf.js"
import { usePage, router } from "@inertiajs/vue3"
import dayjs from 'dayjs'
import { formatCurrency } from '@/utils/utility'
import { route } from 'ziggy-js'

const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
  balance: {
    type: Number,
    default: null,
  },
  company: Object,
  details: Array,
  payment: Object,
})

const page = usePage()
const receiptContent = ref(null)

const filteredCard = computed(() => {
  return props.details.filter((a) => a.card_id !== null)
})

const filteredCash = computed(() => {
  return props.details.filter((a) => a.card_id == null)
})

const parkinDate = computed(() => {
  return props.ticket?.park_datetime
    ? dayjs(props.ticket.park_datetime).format("MM/DD/YYYY")
    : null
})

const parkinTime = computed(() => {
  return props.ticket?.park_datetime
    ? dayjs(props.ticket.park_datetime).format("hh:mm A")
    : null
})

const parkoutDate = computed(() => {
  return props.ticket?.park_out_datetime
    ? dayjs(props.ticket.park_out_datetime).format("MM/DD/YYYY")
    : null
})

const parkoutTime = computed(() => {
  return props.ticket?.park_out_datetime
    ? dayjs(props.ticket.park_out_datetime).format("hh:mm A")
    : null
})

const duration = computed(() => {
  if (!props.ticket?.park_datetime || !props.ticket?.park_out_datetime) return null

  const start = dayjs(props.ticket.park_datetime).startOf('minute')
  const end = dayjs(props.ticket.park_out_datetime).startOf('minute')

  const diffMinutes = end.diff(start, 'minute')
  const days = Math.floor(diffMinutes / (60 * 24))
  const hours = Math.floor((diffMinutes % (60 * 24)) / 60)
  const minutes = diffMinutes % 60

  if (days > 0) {
    return `${days}d ${hours}h ${minutes}m`
  } else if (hours > 0) {
    return `${hours}h ${minutes}m`
  } else {
    return `${minutes}m`
  }
})

const printReceipt = () => {
  window.print()
}

const goToDashboard = () => {
  router.visit(route('dashboard'))
}
</script>

<style scoped>
.receipt-wrapper {

  min-height: 100vh;
  padding: 2rem 0;
}

.text-white-70 {
  color: rgba(255, 255, 255, 0.9);
}

/* Success Header */
.success-header {
  animation: fadeInDown 0.6s ease-out;
}

.success-avatar {
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.4);
  animation: successPulse 2s ease-in-out infinite;
}

@keyframes successPulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 8px 24px rgba(76, 175, 80, 0.4);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 12px 32px rgba(76, 175, 80, 0.6);
  }
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

/* Receipt Card */
.receipt-card {
  background: white;
  animation: fadeInUp 0.8s ease-out;
  max-width: 600px;
  margin: 0 auto;
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

.company-header {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
}

/* Receipt Body */
.section-title {
  font-size: 1rem;
  font-weight: 700;
  color: #1a237e;
  display: flex;
  align-items: center;
}

.info-card,
.payment-card {
  border: 1px solid #e0e0e0;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.label {
  font-size: 0.875rem;
  color: #666;
}

.value {
  font-size: 0.875rem;
  font-weight: 600;
  color: #333;
}

.time-badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  margin-top: 4px;
}

.time-badge.success {
  background: #e8f5e9;
  color: #2e7d32;
}

.time-badge.error {
  background: #ffebee;
  color: #c62828;
}

/* Cards Section */
.card-item {
  background: white;
  border: 1px solid #e0e0e0;
  transition: all 0.2s ease;
}

.card-item:hover {
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Change Card */
.change-card {
  border: 2px solid #81c784;
}

/* Thank You Card */
.thank-you-card {
  border: 2px solid #c5cae9;
}

/* Action Buttons */
.action-buttons {
  animation: fadeIn 1s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Print Styles */
@media print {
  .receipt-wrapper {
    background: white;
  }

  .success-header,
  .action-buttons {
    display: none;
  }

  .receipt-card {
    box-shadow: none;
    max-width: 100%;
  }
}

/* Responsive */
@media (max-width: 600px) {
  .success-header h1 {
    font-size: 1.75rem;
  }

  .success-header p {
    font-size: 1rem;
  }
}
</style>
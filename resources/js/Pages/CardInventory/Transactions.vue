<template>
  <v-dialog v-model="dialog" max-width="500" persistent scrollable>
    <v-card rounded="lg" class="transactions-dialog">
      <!-- Header -->
      <div class="dialog-header">
        <v-toolbar flat density="comfortable" color="indigo-darken-4">
          <v-toolbar-title class="d-flex align-center">
            <v-icon class="mr-2" color="white">mdi-history</v-icon>
            <span class="text-white font-weight-medium">Transaction History</span>
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon="mdi-close" variant="text" color="white" @click="dialog = false"></v-btn>
        </v-toolbar>

        <!-- Card Info Banner -->
        <div class="card-info-banner pa-4">
          <div class="d-flex align-center justify-space-between">
            <div class="d-flex align-center">
              <v-avatar color="white" size="48" class="mr-3">
                <v-icon size="28" color="indigo-darken-4">mdi-credit-card</v-icon>
              </v-avatar>
              <div>
                <p class="text-caption text-white-70 mb-1">Card Number</p>
                <p class="text-h6 font-weight-bold text-white mb-0">
                  {{ props.selectedCard.card_number }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <v-divider></v-divider>

      <!-- Balance Display -->
      <div class="balance-section pa-6 text-center">
        <v-card class="balance-card elevation-0" color="indigo-lighten-5" rounded="lg">
          <div class="pa-4">
            <p class="text-caption text-medium-emphasis mb-2">Current Balance</p>
            <p class="text-h4 font-weight-bold text-indigo-darken-4 mb-0">
              ₱{{ Number(props.selectedCard.balance).toFixed(2) }}
            </p>
          </div>
        </v-card>
      </div>

      <!-- Content -->
      <v-card-text class="transactions-content pa-6 pt-0">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <v-progress-circular indeterminate color="indigo-darken-4" size="64"></v-progress-circular>
          <p class="text-body-2 text-medium-emphasis mt-4">Loading transactions...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <v-icon size="64" color="error" class="mb-4">mdi-alert-circle-outline</v-icon>
          <p class="text-h6 font-weight-medium mb-2">Error Loading Data</p>
          <p class="text-body-2 text-medium-emphasis">{{ error }}</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="Object.keys(groupedTransactions).length === 0" class="text-center py-12">
          <v-icon size="80" color="grey-lighten-2" class="mb-4">mdi-inbox-outline</v-icon>
          <p class="text-h6 font-weight-medium mb-2">No Transactions Yet</p>
          <p class="text-body-2 text-medium-emphasis">There are no transaction records for this card.</p>
        </div>

        <!-- Transactions List -->
        <div v-else>
          <div v-for="(dayTransactions, date) in groupedTransactions" :key="date" class="day-group mb-6">
            <!-- Date Header -->
            <div class="date-header mb-3">
              <v-chip color="indigo-lighten-5" variant="flat" size="small">
                <v-icon start size="14">mdi-calendar</v-icon>
                {{ date }}
              </v-chip>
            </div>

            <!-- Transaction Cards -->
            <v-card
              v-for="t in dayTransactions"
              :key="t.id"
              class="transaction-card elevation-2 mb-3"
              rounded="lg"
            >
              <div class="pa-4">
                <div class="d-flex align-center justify-space-between">
                  <!-- Left: Vehicle Info -->
                  <div class="d-flex align-center flex-grow-1">
                    <v-avatar color="indigo-lighten-5" size="48" class="mr-3">
                      <v-icon size="28" color="indigo-darken-4">mdi-car</v-icon>
                    </v-avatar>
                    <div>
                      <div class="d-flex align-center mb-1">
                        <span class="text-caption text-medium-emphasis mr-1">Plate:</span>
                        <span class="text-subtitle-2 font-weight-bold text-indigo-darken-4">
                          {{ t.payment?.ticket?.plate_no || 'N/A' }}
                        </span>
                      </div>
                      <div class="d-flex align-center">
                        <span class="text-caption text-medium-emphasis mr-1">Ticket:</span>
                        <span class="text-caption text-medium-emphasis">
                          {{ t.payment?.ticket?.ticket_no || 'N/A' }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Right: Amount -->
                  <div class="text-right">
                    <v-chip color="error" variant="flat" size="small" class="font-weight-bold">
                      <v-icon start size="14">mdi-minus</v-icon>
                      ₱{{ Number(t.amount).toFixed(2) }}
                    </v-chip>
                  </div>
                </div>

                <!-- Status Badge -->
                <div class="mt-3 d-flex align-center">
                  <v-chip
                    :color="t.payment?.status === 'Paid' ? 'success' : 'warning'"
                    variant="tonal"
                    size="x-small"
                    class="mr-2"
                  >
                    <v-icon start size="12">
                      {{ t.payment?.status === 'Paid' ? 'mdi-check-circle' : 'mdi-clock-outline' }}
                    </v-icon>
                    {{ t.payment?.status || 'Pending' }}
                  </v-chip>
                  <span class="text-caption text-medium-emphasis">
                    {{ formatTime(t.created_at) }}
                  </span>
                </div>
              </div>
            </v-card>
          </div>
        </div>
      </v-card-text>

      <v-divider></v-divider>

      <!-- Footer -->
      <v-card-actions class="pa-4">
        <v-spacer></v-spacer>
        <v-btn color="indigo-darken-4" variant="flat" @click="dialog = false">
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import { route } from 'ziggy-js'
import { WordformatDate } from '../../utils/utility'
import dayjs from 'dayjs'

const props = defineProps({
  selectedCard: Object
})

const dialog = defineModel({ type: Boolean, default: false })

const transactions = ref([])
const loading = ref(false)
const error = ref(null)

// Group by day
const groupedTransactions = computed(() => {
  return transactions.value.reduce((acc, t) => {
    const day = WordformatDate(t.created_at, "MMMM DD, YYYY")
    if (!acc[day]) acc[day] = []
    acc[day].push(t)
    return acc
  }, {})
})

const formatTime = (datetime) => {
  return dayjs(datetime).format('h:mm A')
}

onMounted(async () => {
  loading.value = true
  try {
    const { data } = await axios.get(route('transactions', props.selectedCard.id))
    transactions.value = data.transactions
  } catch (err) {
    console.error('❌ Fetch error:', err)
    error.value = err.response?.data?.message || 'Failed to load transactions'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.transactions-dialog {
  overflow: hidden;
}

.dialog-header {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
}

.text-white-70 {
  color: rgba(255, 255, 255, 0.7);
}

.card-info-banner {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.balance-section {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.02) 0%, transparent 100%);
}

.balance-card {
  border: 2px solid #c5cae9;
}

.transactions-content {
  max-height: 500px;
  overflow-y: auto;
}

.transactions-content::-webkit-scrollbar {
  width: 6px;
}

.transactions-content::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.transactions-content::-webkit-scrollbar-thumb {
  background: #1a237e;
  border-radius: 10px;
}

.day-group {
  animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.date-header {
  position: sticky;
  top: 0;
  z-index: 1;
  background: white;
  padding: 8px 0;
}

.transaction-card {
  background: white;
  border: 1px solid rgba(0, 0, 0, 0.08);
  transition: all 0.2s ease;
}

.transaction-card:hover {
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
}

/* Loading and Empty States */
.text-center {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
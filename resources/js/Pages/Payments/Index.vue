<template>
  <div class="transactions-wrapper">
    <v-container class="py-8 px-6">
      <!-- Page Header -->
      <div class="page-header mb-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-indigo-darken-4 mb-2">
            <v-icon size="32" class="mr-2">mdi-receipt-text</v-icon>
            Transaction History
          </h1>
          <p class="text-body-1 text-medium-emphasis mb-4">
            View all payment transactions and records
          </p>

          <!-- Filters Section -->
          <v-row align="center">
            <!-- Date From -->
            <v-col cols="12" sm="6" md="3">
              <v-date-input
                v-model="dateFrom"
                prepend-icon=""
                label="Date From"
                prepend-inner-icon="$calendar"
                density="comfortable"
                hide-details="auto"
                variant="outlined"
                bg-color="white"
              />
            </v-col>

            <!-- Date To -->
            <v-col cols="12" sm="6" md="3">
              <v-date-input
                v-model="dateTo"
                prepend-icon=""
                label="Date To"
                prepend-inner-icon="$calendar"
                density="comfortable"
                hide-details="auto"
                variant="outlined"
                bg-color="white"
              />
            </v-col>

            <!-- Payment Type Filter -->
            <v-col cols="12" sm="6" md="3">
              <v-select
                v-model="filterType"
                :items="['All', 'Ticket', 'Card']"
                label="Payment Type"
                density="comfortable"
                variant="outlined"
                bg-color="white"
                hide-details
              />
            </v-col>

            <!-- Search Button -->
             <v-col cols="12" md="3">
        <v-btn color="primary" @click="searchTransactions" :loading="loading">
          Search
        </v-btn>
      </v-col>
          </v-row>
        </div>
      </div>

      <!-- Stats Summary Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="success">mdi-cash-check</v-icon>
                <v-chip size="small" color="success" variant="flat">Total</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ payments.data?.filter(p => p.status === 'Paid').length || 0 }}
              </h3>
              <p class="text-caption text-medium-emphasis">Completed Payments</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="info">mdi-cash-multiple</v-icon>
                <v-chip size="small" color="info" variant="flat">Cash</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ payments.data?.filter(p => p.payment_type === 'Cash').length || 0 }}
              </h3>
              <p class="text-caption text-medium-emphasis">Cash Transactions</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="primary">mdi-credit-card</v-icon>
                <v-chip size="small" color="primary" variant="flat">Card</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ payments.data?.filter(p => p.payment_type === 'Card').length || 0 }}
              </h3>
              <p class="text-caption text-medium-emphasis">Card Transactions</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="warning">mdi-calendar-today</v-icon>
                    <v-btn 
                variant="text" 
                color="indigo-darken-4" 
                size="small"
                append-icon="mdi-arrow-right"
                 @click="previewReport"
              >
              
                View Report
              </v-btn>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ getTodayCount() }}
              </h3>
              <p class="text-caption text-medium-emphasis">Today's Transactions</p>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Main Data Table Card -->
      <v-card class="data-table-card elevation-8" rounded="lg">
        <!-- Export Button Section -->
        <div class="export-section pa-4 text-right">
          <v-menu>
            <v-list>
              <v-list-item @click="exportData('excel')">
                <template v-slot:prepend>
                  <v-icon>mdi-file-excel</v-icon>
                </template>
                <v-list-item-title>Export to Excel</v-list-item-title>
              </v-list-item>
              <v-list-item @click="exportData('pdf')">
                <template v-slot:prepend>
                  <v-icon>mdi-file-pdf-box</v-icon>
                </template>
                <v-list-item-title>Export to PDF</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </div>

        <v-divider></v-divider>

        <!-- Data Table -->
         <v-data-table
          :headers="headers"
          :items="props.payments"
         hide-default-footer
        :items-per-page="props.payments.length"
          class="custom-data-table"
        >
          <template v-slot:item.id="{ item }">
            <span class="font-weight-bold text-indigo-darken-4">#{{ item.id }}</span>
          </template>

          <template v-slot:item.amount="{ item }">
            <span class="font-weight-medium">{{ formatCurrency(item.amount) }}</span>
          </template>

          <template v-slot:item.total_amount="{ item }">
            <span class="font-weight-bold text-indigo-darken-4">
              {{ formatCurrency(item.total_amount) }}
            </span>
          </template>

          <template v-slot:item.change="{ item }">
            <span class="font-weight-medium text-success">
              {{ formatCurrency(item.change) }}
            </span>
          </template>

          <template v-slot:item.payment_type="{ item }">
            <v-chip
              :color="getPaymentTypeColor(item.payment_type)"
              variant="flat"
              size="small"
              class="font-weight-medium payment-type-chip"
            >
              <v-icon start size="12">
                {{ getPaymentTypeIcon(item.payment_type) }}
              </v-icon>
              {{ item.payment_type }}
            </v-chip>
          </template>

          <template v-slot:item.payment_method="{ item }">
            <div class="d-flex align-center justify-center payment-method-cell">
              <span 
                class="payment-method-dot mr-2" 
                :style="{ backgroundColor: getPaymentMethodDotColor(item.payment_method) }"
              ></span>
              <span class="payment-method-text">{{ item.payment_method || 'N/A' }}</span>
            </div>
          </template>

          <template v-slot:item.status="{ item }">
            <v-chip
              :color="item.status === 'Paid' ? 'success' : 'error'"
              variant="flat"
              size="small"
              class="font-weight-medium"
            >
              <v-icon start size="12">
                {{ item.status === 'Paid' ? 'mdi-check-circle' : 'mdi-close-circle' }}
              </v-icon>
              {{ item.status }}
            </v-chip>
          </template>

          <template v-slot:item.paid_at="{ item }">
            <div class="d-flex align-center">
              <v-icon size="16" class="mr-1 text-medium-emphasis">mdi-calendar</v-icon>
              <span class="text-body-2">{{ formatDate(item.paid_at) }}</span>
            </div>
          </template>

          <template v-slot:item.actions="{ item }">
            <v-btn
              icon
              variant="text"
              color="indigo-darken-4"
              size="small"
              @click="viewDetails(item)"
            >
              <v-icon>mdi-eye</v-icon>
              <v-tooltip activator="parent" location="top">View Details</v-tooltip>
            </v-btn>
          </template>
        </v-data-table>
      </v-card>

      <!-- Payment Details Dialog -->
      <v-dialog v-model="detailsDialog" max-width="800px" scrollable>
        <v-card>
          <v-card-title class="pa-4 bg-indigo-darken-4 text-white">
            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <v-icon class="mr-2">mdi-receipt-text</v-icon>
                <span class="text-h6">Payment Details - #{{ selectedPayment?.id }}</span>
              </div>
              <v-btn
                icon
                variant="text"
                @click="closeDetailsDialog"
                color="white"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>
          </v-card-title>

          <v-divider></v-divider>

          <v-card-text class="pa-6" v-if="selectedPayment">
            <!-- Payment Summary -->
            <div class="mb-6">
              <h3 class="text-h6 font-weight-bold mb-4 text-indigo-darken-4">
                <v-icon class="mr-2">mdi-information</v-icon>
                Payment Summary
              </h3>
              <v-row>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value">{{ selectedPayment.customer || 'N/A' }}</span>
                  </div>
                </v-col>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Source:</span>
                    <v-chip
                      :color="getPaymentTypeColor(selectedPayment.payment_type)"
                      variant="flat"
                      size="small"
                      class="ml-2"
                    >
                      {{ selectedPayment.payment_type }}
                    </v-chip>
                  </div>
                </v-col>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Amount Received:</span>
                    <span class="detail-value font-weight-bold">{{ formatCurrency(selectedPayment.amount) }}</span>
                  </div>
                </v-col>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value font-weight-bold text-indigo-darken-4">
                      {{ formatCurrency(selectedPayment.total_amount) }}
                    </span>
                  </div>
                </v-col>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Change:</span>
                    <span class="detail-value text-success font-weight-bold">
                      {{ formatCurrency(selectedPayment.change) }}
                    </span>
                  </div>
                </v-col>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Payment Method:</span>
                    <div class="d-flex align-center">
                      <span 
                        class="payment-method-dot mr-2" 
                        :style="{ backgroundColor: getPaymentMethodDotColor(selectedPayment.payment_method) }"
                      ></span>
                      <span class="detail-value">{{ selectedPayment.payment_method || 'N/A' }}</span>
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Days Deducted:</span>
                    <span class="detail-value">{{ selectedPayment.days_deducted || 0 }} days</span>
                  </div>
                </v-col>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <v-chip
                      :color="selectedPayment.status === 'Paid' ? 'success' : 'error'"
                      variant="flat"
                      size="small"
                      class="ml-2"
                    >
                      {{ selectedPayment.status }}
                    </v-chip>
                  </div>
                </v-col>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Processed By:</span>
                    <span class="detail-value">{{ selectedPayment.user.name || 'N/A' }}</span>
                  </div>
                </v-col>
                <v-col cols="12" sm="6">
                  <div class="detail-item">
                    <span class="detail-label">Payment Date:</span>
                    <span class="detail-value">{{ formatDate(selectedPayment.paid_at) }}</span>
                  </div>
                </v-col>
              </v-row>
            </div>

            <v-divider class="my-6"></v-divider>

            <!-- Payment Details (Card Information) -->
            <div v-if="selectedPayment.details && selectedPayment.details.length > 0">
              <h3 class="text-h6 font-weight-bold mb-4 text-indigo-darken-4">
                <v-icon class="mr-2">mdi-credit-card-outline</v-icon>
                Card Payment Details
              </h3>
              
              <v-table class="details-table">
                <thead>
                  <tr>
                    <th>Card Name</th>
                    <th>Card Number</th>
                    <th class="text-center">No. of Days</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Balance</th>
                    <th class="text-right">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="detail in selectedPayment.details" :key="detail.id">
                    <td>{{ detail.card_name || 'N/A' }}</td>
                    <td>
                      <span class="font-mono">{{ detail.card_number || 'N/A' }}</span>
                    </td>
                    <td class="text-center">{{ detail.no_of_days || 0 }}</td>
                    <td class="text-right">{{ formatCurrency(detail.discount) }}</td>
                    <td class="text-right">{{ formatCurrency(detail.balance) }}</td>
                    <td class="text-right font-weight-bold">{{ formatCurrency(detail.amount) }}</td>
                  </tr>
                </tbody>
              </v-table>
            </div>

            <div v-else>
              <v-alert type="info" variant="tonal" class="mt-4">
                <v-icon class="mr-2">mdi-information</v-icon>
                No additional card payment details available for this transaction.
              </v-alert>
            </div>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-4">
            <v-spacer></v-spacer>
            <v-btn
              color="indigo-darken-4"
              variant="flat"
              @click="closeDetailsDialog"
            >
              Close
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed, defineProps } from 'vue'
import dayjs from 'dayjs'
import { router, usePage } from '@inertiajs/vue3'
import { formatCurrency, formatDate } from '../../utils/utility'
import axios from 'axios'   

const headers = [
  { key: 'id', title: 'ID', sortable: true, width: '80px' },
  { key: 'amount', title: 'Amount Received', align: 'end', width: '140px' },
  { key: 'total_amount', title: 'Total Amount', align: 'end', width: '140px' },
  { key: 'change', title: 'Change', align: 'end', width: '120px' },
  { key: 'payment_type', title: 'Source', align: 'center', width: '150px' },
  { key: 'payment_method', title: 'Payment Method', align: 'center', width: '150px' },
  { key: 'status', title: 'Status', align: 'center', width: '120px' },
  { key: 'paid_at', title: 'Date & Time', width: '180px' },
  { key: 'actions', title: 'Details', align: 'center', width: '100px', sortable: false },
]

// const page = usePage()
// const payments = computed(() => page.props.payments)

const props = defineProps({
  payments: Array,
  filters: Object,
})

const today = dayjs().format('YYYY-MM-DD')

const searchQuery = ref('')
const filterStatus = ref('All')

const itemsPerPage = ref(10)
const pageNumber = ref(1)
const payments = ref(props.payments.data || [])
const loading = ref(false)

// Dialog state
const detailsDialog = ref(false)
const selectedPayment = ref(null)
// const dateFrom = ref(today)
// const dateTo = ref(today)
// const filterType = ref('All')

// const handleSearch = () => {
//   applyFilters()
// }



// const searchTransactions = async () => {
//   try {
//     loading.value = true

//     const params = {
//       dateFrom: dateFrom.value,
//       dateTo: dateTo.value,
//       type: filterType.value, // 'Card', 'Ticket', or 'All'
//     }

//     const { data } = await axios.get('/api/payments/search', { params })

//     if (data.success) {
//       payments.value = data.data
//     } else {
//       payments.value = []
//     }
//   } catch (error) {
//     console.error('Error fetching transactions:', error)
//   } finally {
//      loading.value = false
//   }
// }


// Reactive filters (start with props from server)
const dateFrom = ref(props.filters.dateFrom)
const dateTo = ref(props.filters.dateTo)
const filterType = ref(props.filters.type)

// Function to trigger Inertia visit (re-fetch)
const searchTransactions = () => {
  router.get(route('payments.index'), {
    dateFrom: dateFrom.value,
    dateTo: dateTo.value,
    type: filterType.value,
  }, {
    preserveState: false, // keep component state
    replace: true,       // don’t push history entries
  })
}



const previewReport = () => {
  const params = new URLSearchParams({
    dateFrom: dayjs(dateFrom.value).format("YYYY-MM-DD"),
    dateTo: dayjs(dateTo.value).format("YYYY-MM-DD"),
    type: filterType.value || "",
  }).toString();

  window.open(`${route("reports.transaction.preview")}?${params}`, "_blank");
};


  // Use Inertia to navigate with filters
  // Uncomment and adjust the route name as needed:
  // router.get(route('payments.index'), params, {
  //   preserveState: true,
  //   preserveScroll: true,
  // })


const getTodayCount = () => {
  const today = dayjs().format('YYYY-MM-DD')
  return props.payments.filter(p => 
    dayjs(p.paid_at).format('YYYY-MM-DD') === today
  ).length || 0
}

const getPaymentTypeColor = (type) => {
  switch(type) {
    case 'Card': return 'primary'
    case 'Ticket': return 'warning'
    default: return 'grey'
  }
}

const getPaymentTypeIcon = (type) => {
  switch(type) {
    case 'Card': return 'mdi-credit-card'
    case 'Ticket': return 'mdi-ticket'
    default: return 'mdi-help'
  }
}


const getPaymentMethodDotColor = (method) => {
  switch(method) {
    case 'Cash': return '#4caf50'  // Green
    case 'GCash': return '#2196f3' // Blue
    case 'Visa': return '#3f51b5'  // Indigo
    case 'Mastercard': return '#3f51b5' // Indigo
    case 'PayMaya': return '#4caf50' // Green
    default: return '#9e9e9e' // Grey
  }
}

const exportData = (format) => {
  console.log(`Exporting data as ${format}`)
  // Add export functionality here
}

const viewDetails = (payment) => {
  selectedPayment.value = payment
  detailsDialog.value = true
}

const closeDetailsDialog = () => {
  detailsDialog.value = false
  selectedPayment.value = null
}
</script>

<style scoped>
.transactions-wrapper {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

.page-header {
  width: 100%;
  gap: 1rem;
}

/* Filters Card */
.filters-card {
  background: white;
  border-left: 4px solid #1a237e;
}

/* Stat Cards */
.stat-card {
  background: white;
  border-left: 4px solid transparent;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}

.stat-card:nth-child(1) {
  border-left-color: #4caf50;
}

.stat-card:nth-child(2) {
  border-left-color: #2196f3;
}

.stat-card:nth-child(3) {
  border-left-color: #1a237e;
}

.stat-card:nth-child(4) {
  border-left-color: #ff9800;
}

/* Data Table Card */
.data-table-card {
  background: white;
  overflow: hidden;
}

.export-section {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.03) 0%, rgba(26, 35, 126, 0.01) 100%);
}

/* Payment Type Chip - Full Width */
.payment-type-chip {
  width: 100%;
  justify-content: center;
}

/* Custom Data Table Styles */
:deep(.custom-data-table) {
  background: transparent;
}

:deep(.custom-data-table .v-table__wrapper) {
  background: white;
}

:deep(.custom-data-table thead) {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
}

:deep(.custom-data-table thead th) {
  color: white !important;
  font-weight: 600 !important;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

:deep(.custom-data-table tbody tr) {
  transition: background-color 0.2s ease;
}

:deep(.custom-data-table tbody tr:hover) {
  background: rgba(26, 35, 126, 0.04) !important;
}

/* Details Dialog Styles */
.detail-item {
  padding: 8px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.detail-label {
  font-weight: 600;
  color: #616161;
  min-width: 140px;
}

.detail-value {
  color: #212121;
}

.details-table {
  background: #f5f5f5;
  border-radius: 8px;
  overflow: hidden;
}

.details-table thead {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
}

.details-table thead th {
  color: white !important;
  font-weight: 600 !important;
  padding: 12px !important;
}

.details-table tbody tr {
  background: white;
}

.details-table tbody tr:hover {
  background: rgba(26, 35, 126, 0.04);
}

.details-table tbody td {
  padding: 12px !important;
}

.font-mono {
  font-family: 'Courier New', monospace;
  letter-spacing: 1px;
}

/* Payment Method Dot */
.payment-method-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
  flex-shrink: 0;
}

.payment-method-cell {
  min-width: 120px;
}

.payment-method-text {
  font-weight: 500;
  color: #212121;
  white-space: nowrap;
}

/* Responsive Design */
@media (max-width: 960px) {
  .page-header {
    text-align: center;
  }

  .page-header > div {
    width: 100%;
  }
  
  .search-btn {
    width: 100%;
    margin-top: 0.5rem;
  }
}

/* Animations */
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

.v-card {
  animation: fadeInUp 0.5s ease-out;
}
</style>
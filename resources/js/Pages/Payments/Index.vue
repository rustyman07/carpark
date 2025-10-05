<template>
  <div class="transactions-wrapper">
    <v-container  class="py-8 px-6">
      <!-- Page Header -->
      <div class="page-header mb-8">
        <div>
          <h1 class="text-h4 font-weight-bold text-indigo-darken-4 mb-2">
            <v-icon size="32" class="mr-2">mdi-receipt-text</v-icon>
            Transaction History
          </h1>
          <p class="text-body-1 text-medium-emphasis">
            View all payment transactions and records
          </p>
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
                <v-chip size="small" color="warning" variant="flat">Today</v-chip>
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
        <!-- Filters Section -->
        <div class="filters-section pa-6 pb-4">
          <v-row align="center">
            <v-col cols="12" md="4">
              <v-text-field
                v-model="searchQuery"
                prepend-inner-icon="mdi-magnify"
                label="Search transactions"
                density="comfortable"
                hide-details="auto"
                variant="outlined"
                bg-color="white"
                clearable
              />
            </v-col>

            <v-col cols="12" md="6">
              <v-row>
                <v-col cols="12" sm="6">
                  <v-select
                    v-model="filterStatus"
                    :items="['All', 'Paid', 'Pending']"
                    label="Filter by Status"
                    density="comfortable"
                    variant="outlined"
                    bg-color="white"
                    hide-details
                  />
                </v-col>
                <v-col cols="12" sm="6">
                  <v-select
                    v-model="filterType"
                    :items="['All', 'Cash', 'Card', 'Combination']"
                    label="Payment Type"
                    density="comfortable"
                    variant="outlined"
                    bg-color="white"
                    hide-details
                  />
                </v-col>
              </v-row>
            </v-col>

            <v-col cols="12" md="2" class="text-right">
              <v-menu>
                <template v-slot:activator="{ props }">
                  <v-btn
                    v-bind="props"
                    color="indigo-darken-4"
                    variant="outlined"
                    prepend-icon="mdi-download"
                  >
                    Export
                  </v-btn>
                </template>
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
            </v-col>
          </v-row>
        </div>

        <v-divider></v-divider>

        <!-- Data Table -->
        <v-data-table-server
          :headers="headers"
          :items="payments.data"
          v-model:items-per-page="itemsPerPage"
          :page="pageNumber"
          :items-length="payments.total"
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
              class="font-weight-medium"
            >
              <v-icon start size="12">
                {{ getPaymentTypeIcon(item.payment_type) }}
              </v-icon>
              {{ item.payment_type }}
            </v-chip>
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
        </v-data-table-server>
      </v-card>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import dayjs from 'dayjs'
import { usePage } from '@inertiajs/vue3'
import { formatCurrency, formatDate } from '../../utils/utility'

const headers = [
  { key: 'id', title: 'ID', sortable: true },
  { key: 'amount', title: 'Cash Amount', align: 'end' },
  { key: 'total_amount', title: 'Total Amount', align: 'end' },
  { key: 'change', title: 'Change', align: 'end' },
  { key: 'payment_type', title: 'Payment Type', align: 'center' },
  { key: 'status', title: 'Status', align: 'center' },
  { key: 'paid_at', title: 'Date & Time' },
]

const page = usePage()
const payments = computed(() => page.props.payments)

const searchQuery = ref('')
const filterStatus = ref('All')
const filterType = ref('All')
const itemsPerPage = ref(10)
const pageNumber = ref(1)

const getTodayCount = () => {
  const today = dayjs().format('YYYY-MM-DD')
  return payments.value.data?.filter(p => 
    dayjs(p.paid_at).format('YYYY-MM-DD') === today
  ).length || 0
}

const getPaymentTypeColor = (type) => {
  switch(type) {
    case 'Cash': return 'success'
    case 'Card': return 'primary'
    case 'Combination': return 'warning'
    default: return 'grey'
  }
}

const getPaymentTypeIcon = (type) => {
  switch(type) {
    case 'Cash': return 'mdi-cash'
    case 'Card': return 'mdi-credit-card'
    case 'Combination': return 'mdi-cash-multiple'
    default: return 'mdi-help'
  }
}

const exportData = (format) => {
  console.log(`Exporting data as ${format}`)
  // Add export functionality here
}

const addPayment = () => {
  console.log('Open add payment dialog')
}
</script>

<style scoped>
.transactions-wrapper {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
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

.filters-section {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.03) 0%, rgba(26, 35, 126, 0.01) 100%);
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

/* Responsive Design */
@media (max-width: 960px) {
  .page-header {
    text-align: center;
  }

  .page-header > div {
    width: 100%;
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
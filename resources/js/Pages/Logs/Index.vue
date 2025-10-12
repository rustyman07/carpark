<template>
  <div class="logs-wrapper">
    <v-container  class="py-8 px-6">
      <!-- Page Header -->
      <div class="page-header mb-8">
        <div>
          <h1 class="text-h4 font-weight-bold text-indigo-darken-4 mb-2">
            <v-icon size="32" class="mr-2">mdi-history</v-icon>
            Parking Logs
          </h1>
          <p class="text-body-1 text-medium-emphasis">
            Track and manage all parking transactions
          </p>
        </div>
      </div>

      <!-- Stats Summary Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="success">mdi-car-arrow-right</v-icon>
                <v-chip size="small" color="success" variant="flat">Active</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ items.filter(i => !i.park_out_datetime || i.park_out_datetime === '-').length }}
              </h3>
              <p class="text-caption text-medium-emphasis">Currently Parked</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="info">mdi-car-arrow-left</v-icon>
                <v-chip size="small" color="info" variant="flat">Completed</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ items.filter(i => i.park_out_datetime && i.park_out_datetime !== '-').length }}
              </h3>
              <p class="text-caption text-medium-emphasis">Exited Today</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="warning">mdi-ticket</v-icon>
                <v-chip size="small" color="warning" variant="flat">Total</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ items.length }}
              </h3>
              <p class="text-caption text-medium-emphasis">Total Records</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="error">mdi-alert-circle</v-icon>
                <v-chip size="small" color="error" variant="flat">Voided</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ items.filter(i => i.remarks === 'VOIDED').length }}
              </h3>
              <p class="text-caption text-medium-emphasis">Voided Tickets</p>
            </div>
          </v-card>
        </v-col>
      </v-row>


      <v-col cols="12" sm="6" md="3">
  <v-card class="stat-card elevation-4" rounded="lg">
    <div class="pa-4">
      <div class="d-flex align-center justify-space-between mb-2">
        <v-icon size="32" color="indigo">mdi-cash</v-icon>
       
      </div>
      <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
        ₱ {{ totalParkFee.toLocaleString() }}
      </h3>
      <p class="text-caption text-medium-emphasis">Total Park Fee (per shift)</p>
    </div>
    <div class="kpi-card-footer">
              <v-btn 
                variant="text" 
                color="indigo-darken-4" 
                size="small"
                append-icon="mdi-arrow-right"
                 @click="previeReport"
              >
              
                View Report
              </v-btn>
            </div>
  </v-card>
</v-col>


      <!-- Main Data Table Card -->
      <v-card class="data-table-card elevation-8" rounded="lg">
        <!-- Filters Section -->
        <div class="filters-section pa-6 pb-4">
          <v-row align="center">
            <v-col cols="12" md="3">
              <v-date-input
                v-model="dateFrom"
                prepend-icon=""
                label="From Date"
                prepend-inner-icon="$calendar"
                density="comfortable"
                hide-details="auto"
                variant="outlined"
                bg-color="white"
              />
            </v-col>

            <v-col cols="12" md="2">
              <v-date-input
                v-model="dateTo"
                prepend-icon=""
                label="To Date"
                prepend-inner-icon="$calendar"
                density="comfortable"
                hide-details="auto"
                variant="outlined"
                bg-color="white"
              />
            </v-col>
            <v-col cols="12" md="2">
                <v-select
                label="Shift"
                :items="['MORNING', 'AFTERNOON', 'NIGHT']"
                v-model="selectedShift"
                density="comfortable"
                      hide-details="auto"
                variant="outlined"
                />
            </v-col>

            <v-col cols="12" md="3">
              <v-select
                label="Log Type"
                :items="types"
                item-title="label"
                item-value="value"
                v-model="selectedType"
                density="comfortable"
                hide-details="auto"
                variant="outlined"
                bg-color="white"
                prepend-inner-icon="mdi-filter"
              />
            </v-col>

            <v-col cols="12" md="2">
              <v-btn
                color="indigo-darken-4"
                size="large"
                block
                @click="applyFilter"
                prepend-icon="mdi-magnify"
              >
                Search
              </v-btn>
            </v-col>
          </v-row>
        </div>

        <v-divider></v-divider>

        <!-- Data Table -->
        <v-data-table
          :headers="headers"
          :items="items"
          class="custom-data-table"
          hide-default-footer
          :items-per-page="items.length"
        >
          <template v-slot:item.ticket_no="{ item }">
            <span class="font-weight-bold text-indigo-darken-4">{{ item.ticket_no }}</span>
          </template>

          <template v-slot:item.plate_no="{ item }">
            <v-chip color="indigo-lighten-5" variant="flat" size="small" class="font-weight-bold">
              {{ item.plate_no }}
            </v-chip>
          </template>

          <template v-slot:item.park_datetime="{ item }">
            <div class="d-flex align-center">
              <v-icon size="16" color="success" class="mr-2">mdi-login</v-icon>
              <span class="text-body-2">{{ item.park_datetime }}</span>
            </div>
          </template>

          <template v-slot:item.park_out_datetime="{ item }">
            <div v-if="item.park_out_datetime && item.park_out_datetime !== '-'" class="d-flex align-center">
              <v-icon size="16" color="error" class="mr-2">mdi-logout</v-icon>
              <span class="text-body-2">{{ item.park_out_datetime }}</span>
            </div>
            <v-chip v-else color="success" variant="flat" size="small">
              <v-icon start size="12">mdi-clock-outline</v-icon>
              Active
            </v-chip>
          </template>

          <template v-slot:item.remarks="{ item }">
            <v-chip
              v-if="item.remarks"
              :color="item.remarks === 'VOIDED' ? 'error' : 'info'"
              variant="flat"
              size="small"
              class="font-weight-medium"
            >
              <v-icon start size="12">
                {{ item.remarks === 'VOIDED' ? 'mdi-close-circle' : 'mdi-information' }}
              </v-icon>
              {{ item.remarks }}
            </v-chip>
            <span v-else class="text-medium-emphasis">-</span>
          </template>

          <template v-slot:item.action="{ item }">
            <v-btn
              v-if="item.remarks !== 'VOIDED'"
              color="error"
              size="small"
              variant="outlined"
              @click="deleteTicket(item.id)"
            >
              <v-icon start size="18">mdi-cancel</v-icon>
              Void
            </v-btn>
            <v-chip v-else color="error" variant="flat" size="small" disabled>
              Already Voided
            </v-chip>
          </template>
        </v-data-table>

        <!-- Load More Section -->
        <div v-if="nextPageUrl" class="load-more-section pa-6 text-center">
          <v-divider class="mb-4"></v-divider>
          <v-btn
            color="indigo-darken-4"
            size="large"
            @click="loadMore"
            variant="outlined"
            prepend-icon="mdi-refresh"
          >
            Load More Records
          </v-btn>
        </div>
      </v-card>
    </v-container>

    <!-- Void Confirmation Dialog -->
    <v-dialog v-model="showVoidDialog" max-width="500" persistent>
      <v-card rounded="lg">
        <v-card-title class="pa-6 pb-4">
          <div class="d-flex align-center">
            <v-icon size="32" color="error" class="mr-3">mdi-alert-circle</v-icon>
            <span class="text-h6 font-weight-bold">Confirm Void Ticket</span>
          </div>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-6">
          <v-alert type="warning" variant="tonal" density="comfortable">
            Are you sure you want to void this ticket? This action cannot be undone.
          </v-alert>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="pa-4 justify-end">
          <v-btn variant="outlined" @click="showVoidDialog = false">
            Cancel
          </v-btn>
          <v-btn color="error" variant="flat" @click="confirmVoid">
            Yes, Void Ticket
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref,computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { formatDate } from '../../utils/utility'
import dayjs from 'dayjs'

const props = defineProps({
  Tickets: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
//  totalParkFee: { type: Number, default: 0 },
})



//const totalParkFee = ref(props.totalParkFee)



const items = ref(formatTickets(props.Tickets))
const nextPageUrl = ref(props.Tickets.next_page_url)
const showVoidDialog = ref(false)
const ticketToVoid = ref(null)
const selectedShift = ref('')

const today = dayjs().format('YYYY-MM-DD')
const dateFrom = ref(today)
const dateTo = ref(today)
const selectedType = ref('PARK-IN')

const headers = [
  { key: 'ticket_no', title: 'Ticket No', sortable: true },
  { key: 'plate_no', title: 'Plate No' },
  { key: 'park_datetime', title: 'Park In', sortable: true },
  { key: 'park_out_datetime', title: 'Park Out', sortable: true },
    { key: 'park_fee', title: 'Park Fee', align: 'center' },
  { key: 'remarks', title: 'Remarks', align: 'center' },
  { key: 'action', title: 'Action', align: 'center', sortable: false }
]

const types = [
  { label: 'Park In Records', value: 'PARK-IN' },
  { label: 'Park Out Records', value: 'PARK-OUT' }
]

function formatTickets(tickets) {
    console.log(tickets)
  return (tickets || []).map((ticket) => ({
    ...ticket,
    park_datetime: formatDate(ticket.park_datetime),
    park_out_datetime: formatDate(ticket.park_out_datetime)
  }))
}

const previeReport = () => {
  const params = new URLSearchParams({
    dateFrom: dayjs(dateFrom.value).format("YYYY-MM-DD"),
    dateTo: dayjs(dateTo.value).format("YYYY-MM-DD"),
    shift: selectedShift.value || "",
  }).toString();

  window.open(`${route("reports.parkout.preview")}?${params}`, "_blank");
};



function fetchLogs({ url = "/logs", append = false } = {}) {
  const params = {
    dateFrom: dateFrom.value,
    dateTo: dateTo.value,
    type: selectedType.value,
     shift: selectedShift.value,
  }

  router.get(url, params, {
    preserveState: true,
    preserveScroll: true,
    replace: !append,
   only: ["Tickets", "totalParkFee", "filters"],
    onSuccess: (page) => {
      const formatted = formatTickets(page.props.Tickets)
      if (append) {
        items.value = [...items.value, ...formatted]
      } else {
        items.value = formatted
      }
      nextPageUrl.value = page.props.Tickets.next_page_url
        // totalParkFee.value = page.props.totalParkFee

    },
  })
}

function applyFilter() {
  fetchLogs({ append: false })
}


const totalParkFee = computed(() => {
  if (!items.value.length) return 0;
  return items.value
    .filter(ticket => ticket.remarks === 'Paid')  // <-- Only Paid tickets
    .reduce((sum, ticket) => sum + Number(ticket.park_fee || 0), 0);
});

// function loadMore() {
//   if (nextPageUrl.value) {
//     fetchLogs({ url: nextPageUrl.value, append: true })
//   }
// }


const deleteTicket = (id) => {
  ticketToVoid.value = id
  showVoidDialog.value = true
}

const confirmVoid = () => {
  const id = ticketToVoid.value
  
  items.value = items.value.map(ticket => 
    ticket.id === id ? { ...ticket, remarks: 'VOIDED' } : ticket
  )

  router.delete(route('logs.delete', { id }), {
    onSuccess: () => {
      showVoidDialog.value = false
      ticketToVoid.value = null
      fetchLogs({ append: false })
    },
    onError: (errors) => {
      console.error(errors)
      fetchLogs({ append: false })
    }
  })
}
</script>

<style scoped>
.logs-wrapper {
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
  border-left-color: #ff9800;
}

.stat-card:nth-child(4) {
  border-left-color: #f44336;
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

/* Load More Section */
.load-more-section {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.02) 0%, transparent 100%);
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
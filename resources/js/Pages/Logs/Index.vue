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

      <!-- Main Data Table Card -->
      <v-card class="data-table-card elevation-8" rounded="lg">
        <!-- Filters Section -->
        <div class="filters-section pa-6 pb-4">
          <v-row align="center">
            <v-col cols="12" md="2">
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
            <v-col cols="12" md="3">   
                <v-select
                    label="Staff"
                    :items="[
                        { label: 'All', value: 'All' },
                        ...props.filters.staff.map(s => ({ label: s.name, value: s.id }))
                    ]"
                    item-title="label"
                    item-value="value"
                    v-model="selectedStaff.value"
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
            <div class="d-flex align-center gap-2">
              <v-icon size="16" color="success" class="mr-2">mdi-login</v-icon>
              <span class="text-body-2">{{ item.park_datetime }}</span>
              <v-btn
                v-if="item.uuid"
                icon
                size="x-small"
                color="success"
                variant="text"
                @click="printTicket(item)"
              >
                <v-icon size="18">mdi-printer</v-icon>
              </v-btn>
            </div>
          </template>

          <template v-slot:item.park_out_datetime="{ item }">
            <div v-if="item.park_out_datetime && item.park_out_datetime !== '-'" class="d-flex align-center gap-2">
              <v-icon size="16" color="error" class="mr-2">mdi-logout</v-icon>
              <span class="text-body-2">{{ item.park_out_datetime }}</span>
              <v-btn
                v-if="item.uuid && item.remarks === 'Paid'"
                icon
                size="x-small"
                color="error"
                variant="text"
                @click="printReceipt(item)"
              >
                <v-icon size="18">mdi-printer</v-icon>
              </v-btn>
            </div>
            <v-chip v-else color="success" variant="flat" size="small">
              <v-icon start size="12">mdi-clock-outline</v-icon>
              Active
            </v-chip>
          </template>
          <template v-slot:item.park_fee="{ item }">
            <span class="text-body-2">{{ formatCurrency(item.park_fee) }}</span>
          </template>

          <template v-slot:item.total_amount="{ item }">
            <span class="text-body-2">{{ formatCurrency(item.total_amount) }}</span>
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
            <div class="d-flex gap-2">
              <v-btn
                v-if="$page.props.auth.user.role === 1"
                color="primary"
                size="small"
                variant="outlined"
                @click="editParkIn(item)"
              >
                <v-icon start size="18">mdi-pencil</v-icon>
                Edit
              </v-btn>
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
            </div>
          </template>
          <template v-slot:body.append>
                <tr class="summary-row">
                     <td colspan="2" class="text-left font-weight-bold text-indigo-darken-4">
                       No of Records: {{items.length }}
                    </td>
                    
                    <td></td>
                    <td></td>
                    <td class="  font-weight-bold text-indigo-darken-4">{{ formatCurrency(totalParkFee) }}</td>
                     <td class="  font-weight-bold text-indigo-darken-4">{{ formatCurrency(totalPaid) }}</td>
                    <td></td>
                    <td></td>
                    <td >
                        <v-btn 
                        variant="text" 
                        color="indigo-darken-4" 
                        size="small"
                        append-icon="mdi-arrow-right"
                        @click="previeReport"
                    >
                        View Report
                    </v-btn>
                    </td>
            
                </tr>
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

    <!-- Print Ticket Dialog -->
    <v-dialog v-model="showTicketDialog" max-width="1000px" width="90%" scrollable>
      <v-card class="dialog-card">
        <v-card-title class="d-flex justify-space-between align-center pa-4 bg-indigo-darken-4">
          <span class="text-h6 text-white">Ticket Preview</span>
          <v-btn 
            icon 
            variant="text" 
            @click="closeTicketDialog" 
            color="white"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        
        <v-card-text class="pa-0 pdf-container">
          <iframe
            v-if="ticketPdfUrl"
            :src="ticketPdfUrl"
            class="pdf-iframe"
            title="Ticket PDF Preview"
          />
          <div v-else class="d-flex align-center justify-center loading-container">
            <v-progress-circular
              indeterminate
              color="indigo-darken-4"
              size="64"
            />
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Print Receipt Dialog -->
    <v-dialog v-model="showPrintDialog" max-width="1000px" width="90%" scrollable>
      <v-card class="dialog-card">
        <v-card-title class="d-flex justify-space-between align-center pa-4 bg-indigo-darken-4">
          <span class="text-h6 text-white">Receipt Preview</span>
          <v-btn 
            icon 
            variant="text" 
            @click="closePrintDialog" 
            color="white"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        
        <v-card-text class="pa-0 pdf-container">
          <iframe
            v-if="receiptPdfUrl"
            :src="receiptPdfUrl"
            class="pdf-iframe"
            title="Receipt PDF Preview"
          />
          <div v-else class="d-flex align-center justify-center loading-container">
            <v-progress-circular
              indeterminate
              color="indigo-darken-4"
              size="64"
            />
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>

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

    <!-- Edit Park-In Dialog -->
    <v-dialog v-model="showEditDialog" max-width="600" persistent>
      <v-card rounded="lg">
        <v-card-title class="pa-6 pb-4">
          <div class="d-flex align-center">
            <v-icon size="32" color="primary" class="mr-3">mdi-pencil-circle</v-icon>
            <span class="text-h6 font-weight-bold">Edit Park-In Time</span>
          </div>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-6">
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="editForm.ticket_no"
                label="Ticket No"
                variant="outlined"
                readonly
                density="comfortable"
              />
            </v-col>
            <v-col cols="12">
              <v-text-field
                v-model="editForm.plate_no"
                label="Plate No"
                variant="outlined"
                readonly
                density="comfortable"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="editForm.date"
                label="Date"
                type="date"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-calendar"
                :max="maxDate"
                :error-messages="dateTimeError"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="editForm.time"
                label="Time"
                type="time"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-clock-outline"
                :error-messages="dateTimeError"
              />
            </v-col>
            <v-col cols="12" v-if="dateTimeError">
              <v-alert type="error" variant="tonal" density="compact">
                {{ dateTimeError }}
              </v-alert>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="pa-4 justify-end">
          <v-btn variant="outlined" @click="showEditDialog = false">
            Cancel
          </v-btn>
          <v-btn 
            color="primary" 
            variant="flat" 
            @click="confirmUpdate"
            :disabled="!!dateTimeError"
          >
            Update Park-In
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { formatCurrency, formatDate } from '../../utils/utility'
import dayjs from 'dayjs'
import { route } from 'ziggy-js'

const props = defineProps({
  Tickets: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
})

const items = ref(formatTickets(props.Tickets))
const nextPageUrl = ref(props.Tickets.next_page_url)
const showVoidDialog = ref(false)
const showEditDialog = ref(false)
const showPrintDialog = ref(false)
const showTicketDialog = ref(false)
const ticketToVoid = ref(null)
const selectedTicketUuid = ref(null)
const selectedReceiptUuid = ref(null)
const selectedStaff = ref({ label: 'All', value: 'All' })
const dateTimeError = ref('')
const editForm = ref({
  id: null,
  ticket_no: '',
  plate_no: '',
  date: '',
  time: ''
})

const today = dayjs().format('YYYY-MM-DD')
const maxDate = dayjs().format('YYYY-MM-DD')
const dateFrom = ref(today)
const dateTo = ref(today)
const selectedType = ref('PARK-IN')

const headers = [
  { key: 'ticket_no', title: 'Ticket No', sortable: true },
  { key: 'plate_no', title: 'Plate No' },
  { key: 'park_datetime', title: 'Park In', sortable: true },
  { key: 'park_out_datetime', title: 'Park Out', sortable: true },
  { key: 'park_fee', title: 'Park Fee', align: 'center' },
  { key: 'total_amount', title: 'Total Paid', align: 'center' },
  { key: 'remarks', title: 'Remarks', align: 'center' },
  { key: 'park_out_user.name', title: 'Parked Out By', align: 'center' },
  { key: 'action', title: 'Action', align: 'center', sortable: false }
]

const types = [
  { label: 'Park In Records', value: 'PARK-IN' },
  { label: 'Park Out Records', value: 'PARK-OUT' }
]

// Computed property for Ticket PDF URL
const ticketPdfUrl = computed(() => {
  if (showTicketDialog.value && selectedTicketUuid.value) {
    return route('print.ticket', { uuid: selectedTicketUuid.value })
  }
  return null
})

// Computed property for Receipt PDF URL
const receiptPdfUrl = computed(() => {
  if (showPrintDialog.value && selectedReceiptUuid.value) {
    return route('receipt.print', { uuid: selectedReceiptUuid.value })
  }
  return null
})

// Watch for dialog close and reset ticketToVoid
watch(showVoidDialog, (newVal) => {
  if (!newVal) {
    ticketToVoid.value = null
  }
})

// Watch for changes in date and time to validate
watch([() => editForm.value.date, () => editForm.value.time], () => {
  validateDateTime()
})

function validateDateTime() {
  const { date, time } = editForm.value
  
  if (!date || !time) {
    dateTimeError.value = ''
    return
  }
  
  const selectedDateTime = dayjs(`${date} ${time}`)
  const now = dayjs()
  
  if (selectedDateTime.isAfter(now)) {
    dateTimeError.value = 'Park-in time cannot be in the future'
  } else {
    dateTimeError.value = ''
  }
}

function formatTickets(tickets) {
  return (tickets || []).map((ticket) => ({
    ...ticket,
    park_datetime: formatDate(ticket.park_datetime),
    park_out_datetime: formatDate(ticket.park_out_datetime)
  }))
}

const printTicket = (item) => {
  console.log('Print Ticket - Item:', item)
  console.log('UUID:', item.uuid)
  
  if (item.uuid) {
    selectedTicketUuid.value = item.uuid
    console.log('Generated Ticket URL:', route('print.ticket', { uuid: item.uuid }))
    showTicketDialog.value = true
  } else {
    console.error('No UUID found for ticket:', item)
  }
}

const closeTicketDialog = () => {
  showTicketDialog.value = false
  selectedTicketUuid.value = null
}

const printReceipt = (item) => {
  console.log('Print Receipt - Item:', item)
  console.log('UUID:', item.uuid)
  
  if (item.uuid) {
    selectedReceiptUuid.value = item.uuid
    console.log('Generated Receipt URL:', route('receipt.print', { uuid: item.uuid }))
    showPrintDialog.value = true
  } else {
    console.error('No UUID found for receipt:', item)
  }
}

const closePrintDialog = () => {
  showPrintDialog.value = false
  selectedReceiptUuid.value = null
}

const previeReport = () => {
  const params = new URLSearchParams({
    dateFrom: dayjs(dateFrom.value).format("YYYY-MM-DD"),
    dateTo: dayjs(dateTo.value).format("YYYY-MM-DD"),
    staff: selectedStaff.value.value || "",
  }).toString();

  window.open(`${route("reports.parkout.preview")}?${params}`, "_blank");
};

function fetchLogs({ url = "/logs", append = false } = {}) {
  const params = {
    dateFrom: dayjs(dateFrom.value).format("YYYY-MM-DD"),
    dateTo: dayjs(dateTo.value).format("YYYY-MM-DD"),
    type: selectedType.value,
    staff: selectedStaff.value.value,
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
    },
  })
}

function applyFilter(e) {
  if (e) e.preventDefault();
  fetchLogs({ append: false })
}

const totalParkFee = computed(() => {
  if (!items.value.length) return 0;
  return items.value
    .filter(ticket => ticket.remarks === 'Paid')
    .reduce((sum, ticket) => sum + Number(ticket.park_fee || 0), 0);
});

const totalPaid = computed(() => {
  if (!items.value.length) return 0;
  return items.value
    .filter(ticket => ticket.remarks === 'Paid')
    .reduce((sum, ticket) => sum + Number(ticket.total_amount || 0), 0);
});

const deleteTicket = (id) => {
  const ticket = items.value.find(t => t.id === id)
  
  if (ticket && ticket.remarks === 'VOIDED') {
    return
  }
  
  ticketToVoid.value = id
  showVoidDialog.value = true
}

const confirmVoid = () => {
  const id = ticketToVoid.value
  
  showVoidDialog.value = false
  
  router.delete(route('logs.delete', { id }), {
    onSuccess: () => {
      ticketToVoid.value = null
      fetchLogs({ append: false })
    },
    onError: (errors) => {
      console.error(errors)
      ticketToVoid.value = null
      fetchLogs({ append: false })
    }
  })
}

const editParkIn = (item) => {
  const parkDateTime = dayjs(item.park_datetime, 'MM/DD/YYYY hh:mm A')
  
  editForm.value = {
    id: item.id,
    ticket_no: item.ticket_no,
    plate_no: item.plate_no,
    date: parkDateTime.format('YYYY-MM-DD'),
    time: parkDateTime.format('HH:mm')
  }
  
  dateTimeError.value = ''
  showEditDialog.value = true
}

const confirmUpdate = () => {
  const { id, date, time } = editForm.value
  
  validateDateTime()
  
  if (dateTimeError.value) {
    return
  }
  
  const parkDateTime = dayjs(`${date} ${time}`)
  
  const updateData = {
    park_year: parkDateTime.year(),
    park_month: parkDateTime.month() + 1,
    park_day: parkDateTime.date(),
    park_hour: parkDateTime.hour(),
    park_minute: parkDateTime.minute(),
    park_second: parkDateTime.second()
  }
  
  showEditDialog.value = false
  
  router.put(route('logs.update_parkindatetime', { id }), updateData, {
    onSuccess: () => {
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

.stat-card {
  background: white;
  border-left: 4px solid transparent;
  border-left-color: #1A237E;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}

.data-table-card {
  background: white;
  overflow: hidden;
}

.filters-section {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.03) 0%, rgba(26, 35, 126, 0.01) 100%);
}

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

.load-more-section {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.02) 0%, transparent 100%);
}

/* PDF Dialog Styles */
.pdf-container {
  height: 80vh;
  min-height: 500px;
}

.pdf-iframe {
  width: 100%;
  height: 100%;
  border: none;
}

.loading-container {
  height: 80vh;
  min-height: 500px;
}

@media (max-width: 960px) {
  .page-header {
    text-align: center;
  }

  .page-header > div {
    width: 100%;
  }
  
  .pdf-container,
  .loading-container {
    height: 60vh;
    min-height: 400px;
  }
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

.v-card {
  animation: fadeInUp 0.5s ease-out;
}
</style>
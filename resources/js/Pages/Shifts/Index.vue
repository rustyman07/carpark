<template>
  <div class="shifts-wrapper">
    <v-container  class="py-8 px-6">
      <!-- Page Header -->
      <div class="page-header mb-8">
        <div>
          <h1 class="text-h4 font-weight-bold text-indigo-darken-4 mb-2">
            <v-icon size="32" class="mr-2">mdi-clock-time-eight</v-icon>
            Shift Management
          </h1>
          <p class="text-body-1 text-medium-emphasis">
            Manage work shifts and schedules
          </p>
        </div>
        <v-btn
          color="indigo-darken-4"
          size="large"
          @click="addShift"
          prepend-icon="mdi-plus-circle"
          class="add-btn elevation-4"
        >
          Add Shift
        </v-btn>
      </div>

      <!-- Stats Summary Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="success">mdi-clock-check</v-icon>
                <v-chip size="small" color="success" variant="flat">Active</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ activeShiftsCount }}
              </h3>
              <p class="text-caption text-medium-emphasis">Active Shifts</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="warning">mdi-clock-alert</v-icon>
                <v-chip size="small" color="warning" variant="flat">Inactive</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ inactiveShiftsCount }}
              </h3>
              <p class="text-caption text-medium-emphasis">Inactive Shifts</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="primary">mdi-calendar-clock</v-icon>
                <v-chip size="small" color="primary" variant="flat">Total</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ totalShifts }}
              </h3>
              <p class="text-caption text-medium-emphasis">Total Shifts</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="info">mdi-account-group</v-icon>
                <v-chip size="small" color="info" variant="flat">Staff</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ totalAssignedUsers }}
              </h3>
              <p class="text-caption text-medium-emphasis">Assigned Staff</p>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Create/Edit Dialog -->
      <ShiftFormDialog v-model="showDialog" :selectedShift="selectedShift" />

      <!-- Delete Confirmation Dialog -->
      <v-dialog v-model="showDeleteDialog" max-width="500" persistent>
        <v-card rounded="lg">
          <v-card-title class="pa-6 pb-4">
            <div class="d-flex align-center">
              <v-icon size="32" color="error" class="mr-3">mdi-alert-circle</v-icon>
              <span class="text-h6 font-weight-bold">Confirm Delete</span>
            </div>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-6">
            <v-alert type="error" variant="tonal" density="comfortable">
              Are you sure you want to delete <strong>{{ itemToDelete?.name }}</strong>? This action cannot be undone.
            </v-alert>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions class="pa-4 justify-end">
            <v-btn variant="outlined" @click="showDeleteDialog = false">
              Cancel
            </v-btn>
            <v-btn color="error" variant="flat" @click="confirmDelete">
              Yes, Delete Shift
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Main Data Table Card -->
      <v-card class="data-table-card elevation-8" rounded="lg">
        <div class="card-header pa-6 pb-4">
          <div class="d-flex align-center justify-space-between">
            <h3 class="text-h6 font-weight-bold text-indigo-darken-4">
              <v-icon class="mr-2">mdi-format-list-bulleted</v-icon>
              Shifts List
            </h3>
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              label="Search shifts"
              density="compact"
              hide-details
              variant="outlined"
              bg-color="white"
              clearable
              style="max-width: 300px;"
            />
          </div>
        </div>

        <v-divider></v-divider>

        <!-- Data Table -->
        <v-data-table
          :headers="headers"
          :items="filteredShifts"
          class="custom-data-table"
          :items-per-page="10"
        >
          <template v-slot:item.name="{ item }">
            <div class="d-flex align-center">
              <v-avatar color="indigo-lighten-5" size="40" class="mr-3">
                <v-icon color="indigo-darken-4">mdi-clock-outline</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-bold text-indigo-darken-4">{{ item.name }}</div>
                <div class="text-caption text-medium-emphasis">{{ getShiftDuration(item) }}</div>
              </div>
            </div>
          </template>

          <template v-slot:item.start_time="{ item }">
            <v-chip color="success" variant="flat" size="small">
              <v-icon start size="14">mdi-login</v-icon>
              {{ formatTime(item.start_time) }}
            </v-chip>
          </template>

          <template v-slot:item.end_time="{ item }">
            <v-chip color="error" variant="flat" size="small">
              <v-icon start size="14">mdi-logout</v-icon>
              {{ formatTime(item.end_time) }}
            </v-chip>
          </template>

          <template v-slot:item.is_active="{ item }">
            <v-switch
              v-model="item.is_active"
              color="success"
              density="compact"
              hide-details
              @change="toggleShiftStatus(item)"
            >
              <template v-slot:label>
                <v-chip
                  :color="item.is_active ? 'success' : 'grey'"
                  variant="flat"
                  size="x-small"
                >
                  {{ item.is_active ? 'Active' : 'Inactive' }}
                </v-chip>
              </template>
            </v-switch>
          </template>

          <template v-slot:item.users_count="{ item }">
            <v-chip color="info" variant="tonal" size="small">
              <v-icon start size="14">mdi-account-multiple</v-icon>
              {{ item.users?.length || 0 }} staff
            </v-chip>
          </template>

          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-2">
              <v-btn
                icon="mdi-pencil"
                color="indigo-darken-4"
                size="small"
                variant="tonal"
                @click="editShift(item)"
              ></v-btn>

              <v-btn
                icon="mdi-delete"
                color="error"
                size="small"
                variant="tonal"
                @click="deleteShift(item)"
              ></v-btn>
            </div>
          </template>
        </v-data-table>
      </v-card>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ShiftFormDialog from './ShiftFormDialog.vue'
import dayjs from 'dayjs'

// Sample data - replace with your actual props
const props = defineProps({
  shifts: {
    type: Array,
    default: () => [ ]
  }
})

const showDialog = ref(false)
const showDeleteDialog = ref(false)
const selectedShift = ref(null)
const itemToDelete = ref(null)
const searchQuery = ref('')

const headers = [
  { key: 'name', title: 'Shift Name', sortable: true },
  { key: 'start_time', title: 'Start Time', align: 'center' },
  { key: 'end_time', title: 'End Time', align: 'center' },
  { key: 'users_count', title: 'Assigned Staff', align: 'center' },
  { key: 'is_active', title: 'Status', align: 'center' },
  { key: 'actions', title: 'Actions', align: 'center', sortable: false }
]

const filteredShifts = computed(() => {
  if (!searchQuery.value) return props.shifts

  const query = searchQuery.value.toLowerCase()
  return props.shifts.filter(shift => 
    shift.name?.toLowerCase().includes(query)
  )
})

const activeShiftsCount = computed(() => 
  props.shifts.filter(s => s.is_active).length
)

const inactiveShiftsCount = computed(() => 
  props.shifts.filter(s => !s.is_active).length
)

const totalShifts = computed(() => props.shifts.length)

const totalAssignedUsers = computed(() => 
  props.shifts.reduce((sum, shift) => sum + (shift.users?.length || 0), 0)
)

const formatTime = (time) => {
  return dayjs(`2000-01-01 ${time}`).format('h:mm A')
}

const getShiftDuration = (shift) => {
  const start = dayjs(`2000-01-01 ${shift.start_time}`)
  const end = dayjs(`2000-01-01 ${shift.end_time}`)
  const duration = end.diff(start, 'hour')
  return `${duration} hours`
}

const addShift = () => {
  selectedShift.value = null
  showDialog.value = true
}

const editShift = (shift) => {
  selectedShift.value = shift
  showDialog.value = true
}

const deleteShift = (shift) => {
  itemToDelete.value = shift
  showDeleteDialog.value = true
}

const confirmDelete = () => {
  // router.delete(route('shifts.destroy', itemToDelete.value.id))
  showDeleteDialog.value = false
  itemToDelete.value = null
}

const toggleShiftStatus = (shift) => {
  // router.put(route('shifts.toggle-status', shift.id), {
  //   is_active: shift.is_active
  // })
}
</script>

<style scoped>
.shifts-wrapper {
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

.add-btn {
  transition: all 0.3s ease;
}

.add-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(26, 35, 126, 0.3) !important;
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
  border-left-color: #ff9800;
}

.stat-card:nth-child(3) {
  border-left-color: #1a237e;
}

.stat-card:nth-child(4) {
  border-left-color: #2196f3;
}

/* Data Table Card */
.data-table-card {
  background: white;
  overflow: hidden;
}

.card-header {
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

  .add-btn {
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
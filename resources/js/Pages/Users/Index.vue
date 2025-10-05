<template>
  <div class="users-wrapper">
    <v-container class="py-8 px-6">
      <!-- Page Header -->
      <div class="page-header mb-8">
        <div>
          <h1 class="text-h4 font-weight-bold text-indigo-darken-4 mb-2">
            <v-icon size="32" class="mr-2">mdi-account-group</v-icon>
            User Management
          </h1>
          <p class="text-body-1 text-medium-emphasis">
            Manage system users and their access
          </p>
        </div>
        <v-btn
          color="indigo-darken-4"
          size="large"
          @click="addusers"
          prepend-icon="mdi-plus-circle"
          class="add-btn elevation-4"
        >
          Add User
        </v-btn>
      </div>

      <!-- Stats Summary Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="4">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="success">mdi-account-check</v-icon>
                <v-chip size="small" color="success" variant="flat">Active</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ props.users.length }}
              </h3>
              <p class="text-caption text-medium-emphasis">Total Users</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="4">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="primary">mdi-shield-account</v-icon>
                <v-chip size="small" color="primary" variant="flat">Admin</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ props.users.filter(u => u.role === 'admin').length }}
              </h3>
              <p class="text-caption text-medium-emphasis">Administrators</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="4">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="info">mdi-account</v-icon>
                <v-chip size="small" color="info" variant="flat">Staff</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ props.users.filter(u => u.role !== 'admin').length }}
              </h3>
              <p class="text-caption text-medium-emphasis">Staff Members</p>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Create/Edit Dialog -->
      <Create v-model="showDialog" :selectedItem="selectedItem" />

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
              Yes, Delete User
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Main Data Table Card -->
      <v-card class="data-table-card elevation-8" rounded="lg">
        <div class="card-header pa-6 pb-4">
          <div class="d-flex align-center justify-space-between">
            <h3 class="text-h6 font-weight-bold text-indigo-darken-4">
              <v-icon class="mr-2">mdi-account-multiple</v-icon>
              Users List
            </h3>
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              label="Search users"
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
          :items="filteredUsers"
          class="custom-data-table"
          :items-per-page="10"
        >
          <template v-slot:item.fullname="{ item }">
            <div class="d-flex align-center">
              <v-avatar color="indigo-lighten-5" size="40" class="mr-3">
                <v-icon color="indigo-darken-4">mdi-account</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-bold text-indigo-darken-4">{{ item.name }}</div>
                <div class="text-caption text-medium-emphasis">{{ item.username }}</div>
              </div>
            </div>
          </template>

          <template v-slot:item.username="{ item }">
            <v-chip color="indigo-lighten-5" variant="flat" size="small">
              <v-icon start size="14">mdi-at</v-icon>
              {{ item.username }}
            </v-chip>
          </template>

          <template v-slot:item.contact="{ item }">
            <div class="d-flex align-center" v-if="item.contact">
              <v-icon size="16" color="success" class="mr-2">mdi-phone</v-icon>
              <span class="text-body-2">{{ item.contact }}</span>
            </div>
            <span v-else class="text-medium-emphasis">-</span>
          </template>

          <template v-slot:item.role="{ item }">
            <v-chip
              :color="item.role === 'admin' ? 'error' : 'info'"
              variant="flat"
              size="small"
              class="font-weight-medium"
            >
              <v-icon start size="12">
                {{ item.role === 'admin' ? 'mdi-shield-crown' : 'mdi-account' }}
              </v-icon>
              {{ item.role || 'Staff' }}
            </v-chip>
          </template>

          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-2">
              <v-btn
                icon="mdi-pencil"
                color="indigo-darken-4"
                size="small"
                variant="tonal"
                @click="editItem(item)"
              ></v-btn>

              <v-btn
                icon="mdi-delete"
                color="error"
                size="small"
                variant="tonal"
                @click="deleteItem(item)"
              ></v-btn>
            </div>
          </template>
        </v-data-table>

        <!-- Load More Section -->
        <div v-if="props.users.next_page_url" class="load-more-section pa-6 text-center">
          <v-divider class="mb-4"></v-divider>
          <v-btn
            color="indigo-darken-4"
            size="large"
            @click="loadMore"
            variant="outlined"
            prepend-icon="mdi-refresh"
          >
            Load More Users
          </v-btn>
        </div>
      </v-card>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Create from './Create.vue'

const props = defineProps({
  users: Object,
  auth: Object,
})

const showDialog = ref(false)
const showDeleteDialog = ref(false)
const selectedItem = ref({})
const itemToDelete = ref(null)
const searchQuery = ref('')

const headers = [
  { key: 'fullname', title: 'User', sortable: true },
  { key: 'contact', title: 'Contact' },
  { key: 'role', title: 'Role', align: 'center' },
  { key: 'actions', title: 'Actions', align: 'center', sortable: false }
]

const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users

  const query = searchQuery.value.toLowerCase()
  return props.users.filter(user => 
    user.name?.toLowerCase().includes(query) ||
    user.username?.toLowerCase().includes(query) ||
    user.contact?.toLowerCase().includes(query)
  )
})

const addusers = () => {
  selectedItem.value = {}
  showDialog.value = true
}

const editItem = (item) => {
  selectedItem.value = item
  showDialog.value = true
}

const deleteItem = (item) => {
  itemToDelete.value = item
  showDeleteDialog.value = true
}

const confirmDelete = () => {
  const item = itemToDelete.value
  router.delete(route('users.destroy', item.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      itemToDelete.value = null
    }
  })
}

const loadMore = () => {
  if (!props.users.next_page_url) return
  router.get(props.users.next_page_url, {}, { preserveState: true })
}
</script>

<style scoped>
.users-wrapper {
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
  border-left-color: #1a237e;
}

.stat-card:nth-child(3) {
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
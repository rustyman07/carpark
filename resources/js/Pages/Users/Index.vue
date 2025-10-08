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
            Manage system users, their roles, and assigned shifts
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
                {{ props.users.filter(u => u.role === 1).length }}
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
                {{ props.users.filter(u => u.role !== 1).length }}
              </h3>
              <p class="text-caption text-medium-emphasis">Staff Members</p>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Create/Edit Dialog -->
      <Create v-model="showDialog" :selectedItem="selectedItem" :shifts="props.shifts" />

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
            <v-btn variant="outlined" @click="showDeleteDialog = false">Cancel</v-btn>
            <v-btn color="error" variant="flat" @click="confirmDelete">Yes, Delete User</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Users Data Table -->
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

          <template v-slot:item.contact="{ item }">
            <div class="d-flex align-center" v-if="item.contact">
              <v-icon size="16" color="success" class="mr-2">mdi-phone</v-icon>
              <span class="text-body-2">{{ item.contact }}</span>
            </div>
            <span v-else class="text-medium-emphasis">-</span>
          </template>

          <template v-slot:item.role="{ item }">
            <v-chip
              :color="item.role === 1 ? 'error' : 'info'"
              variant="flat"
              size="small"
              class="font-weight-medium"
            >
              <v-icon start size="12">
                {{ item.role === 1 ? 'mdi-shield-crown' : 'mdi-account' }}
              </v-icon>
              {{ item.role === 1 ? 'Admin' : 'Staff' }}
            </v-chip>
          </template>

          <!-- 🕐 Show assigned shift -->
          <template v-slot:item.shift="{ item }">
            <v-chip color="indigo-lighten-5" size="small" v-if="item.shift">
              <v-icon start size="14">mdi-clock-outline</v-icon>
              {{ item.shift.name }}
            </v-chip>
            <span v-else class="text-medium-emphasis">No Shift</span>
          </template>

          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-2">
              <v-btn icon="mdi-pencil" color="indigo-darken-4" size="small" variant="tonal" @click="editItem(item)" />
              <v-btn icon="mdi-delete" color="error" size="small" variant="tonal" @click="deleteItem(item)" />
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
import Create from './Create.vue'

const props = defineProps({
  users: Array,
  shifts: Array, // 👈 NEW: get shifts from backend
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
  { key: 'shift', title: 'Shift', align: 'center' }, // 🆕 added column
  { key: 'actions', title: 'Actions', align: 'center', sortable: false }
]

const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users
  const q = searchQuery.value.toLowerCase()
  return props.users.filter(u =>
    u.name?.toLowerCase().includes(q) ||
    u.username?.toLowerCase().includes(q) ||
    u.contact?.toString().includes(q) ||
    u.shift?.name?.toLowerCase().includes(q)
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
  router.delete(route('users.destroy', itemToDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      itemToDelete.value = null
    }
  })
}
</script>

<style scoped>
/* same styles as before */
</style>

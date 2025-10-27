<template>
  <div class="users-wrapper">
    <v-container class="py-8 px-6" fluid>
      <!-- Page Header -->
      <div class="page-header mb-8">
        <div>
          <h1 class="text-h4 font-weight-bold text-indigo-darken-4 mb-2">
            <v-icon size="36" class="mr-3" color="indigo-darken-4">mdi-account-group</v-icon>
            User Management
          </h1>
          <p class="text-body-1 text-grey-darken-1">
            Manage system users, their roles
          </p>
        </div>
        <v-btn
          color="indigo-darken-4"
          size="large"
          @click="addusers"
          prepend-icon="mdi-plus-circle"
      
        >
          Add User  
        </v-btn>
      </div>

      <!-- Stats Summary Cards -->
      <!-- <v-row class="mb-8">
        <v-col cols="12" sm="6" lg="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-3">
                <v-avatar color="indigo-lighten-5" size="48">
                  <v-icon size="28" color="indigo-darken-4">mdi-account-check</v-icon>
                </v-avatar>
                <v-chip color="success" variant="flat" size="small">Active</v-chip>
              </div>
              <h3 class="text-h4 font-weight-bold text-indigo-darken-4 mb-1">
                {{ props.users.length }}
              </h3>
              <p class="text-body-2 text-grey-darken-1">Total Users</p>
            </div>
          </v-card>
        </v-col>


        <v-col cols="12" sm="6" lg="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-3">
                <v-avatar color="red-lighten-5" size="48">
                  <v-icon size="28" color="red-darken-2">mdi-shield-crown</v-icon>
                </v-avatar>
                <v-chip color="red-darken-2" variant="flat" size="small">Admin</v-chip>
              </div>
              <h3 class="text-h4 font-weight-bold text-indigo-darken-4 mb-1">
                {{ props.users.filter(u => u.role === 1).length }}
              </h3>
              <p class="text-body-2 text-grey-darken-1">Administrators</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" lg="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-3">
                <v-avatar color="blue-lighten-5" size="48">
                  <v-icon size="28" color="blue-darken-2">mdi-account</v-icon>
                </v-avatar>
                <v-chip color="blue-darken-2" variant="flat" size="small">Staff</v-chip>
              </div>
              <h3 class="text-h4 font-weight-bold text-indigo-darken-4 mb-1">
                {{ props.users.filter(u => u.role === 2).length }}
              </h3>
              <p class="text-body-2 text-grey-darken-1">Staff Members</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" lg="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-3">
                <v-avatar color="purple-lighten-5" size="48">
                  <v-icon size="28" color="purple-darken-2">mdi-file-search</v-icon>
                </v-avatar>
                <v-chip color="purple-darken-2" variant="flat" size="small">Auditor</v-chip>
              </div>
              <h3 class="text-h4 font-weight-bold text-indigo-darken-4 mb-1">
                {{ props.users.filter(u => u.role === 3).length }}
              </h3>
              <p class="text-body-2 text-grey-darken-1">Auditors</p>
            </div>
          </v-card>
        </v-col>
      </v-row> -->

      <!-- Create/Edit Dialog -->
      <Create v-model="showDialog" :selectedItem="selectedItem" :shifts="props.shifts" />

      <!-- Delete Confirmation Dialog -->
      <v-dialog v-model="showDeleteDialog" max-width="550" persistent>
        <v-card rounded="lg" class="elevation-24">
          <v-card-title class="pa-6 pb-4 bg-red-lighten-5">
            <div class="d-flex align-center">
              <v-avatar color="red-darken-2" size="48" class="mr-3">
                <v-icon size="28" color="white">mdi-alert-circle</v-icon>
              </v-avatar>
              <div>
                <div class="text-h6 font-weight-bold text-red-darken-2">Confirm Delete</div>
                <div class="text-caption text-grey-darken-1">This action cannot be undone</div>
              </div>
            </div>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-6">
            <v-alert type="error" variant="tonal" density="comfortable" rounded="lg">
              <div class="text-body-1">
                Are you sure you want to delete <strong class="text-red-darken-2">{{ itemToDelete?.name }}</strong>?
              </div>
              <div class="text-body-2 mt-2 text-grey-darken-1">
                All associated data will be permanently removed from the system.
              </div>
            </v-alert>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions class="pa-6 justify-end">
            <v-btn variant="outlined" size="large" @click="showDeleteDialog = false" rounded="lg">
              Cancel
            </v-btn>
            <v-btn color="red-darken-2" variant="flat" size="large" @click="confirmDelete" prepend-icon="mdi-delete" rounded="lg" class="elevation-4">
              Yes, Delete User
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Users Data Table -->
      <v-card class="data-table-card elevation-4" rounded="lg">
        <div class="card-header pa-4 pb-4">
          <div class="d-flex align-center justify-space-between flex-wrap ">
            
                <h3 class="text-h6 font-weight-bold text-indigo-darken-4">     
                <v-icon class="mr-2" >mdi-account-multiple</v-icon> 
                 Users List
                </h3>
                       
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              label="Search users"
             density="compact"
              hide-details
              variant="outlined"
              clearable
              style="max-width: 350px;"
            color="indigo-darken-4"
            />
          </div>
        </div>

        <v-divider></v-divider>

        <v-data-table
          :headers="headers"
          :items="filteredUsers"
          class="custom-data-table"
            hide-default-footer
   
        >
          <template v-slot:item.fullname="{ item }">
            <div class="d-flex align-center py-3">

              <div>
                <div class="font-weight-bold text-indigo-darken-4 text-body-1">{{ item.name }}</div>
        
              </div>
            </div>
          </template>

          <template v-slot:item.contact="{ item }">
            <div class="d-flex align-center" v-if="item.contact">
              <v-chip color="indigo-lighten-5" variant="flat" size="small" prepend-icon="mdi-phone">
                <span class="text-indigo-darken-4 font-weight-medium">{{ item.contact }}</span>
              </v-chip>
            </div>
            <span v-else class="text-grey-lighten-1">-</span>
          </template>

          <template v-slot:item.role="{ item }">
            <v-chip
              :color="getRoleColor(item.role)"
              variant="flat"
              size="default"
              class="font-weight-bold elevation-2"
            >
              <v-icon start size="16">
                {{ getRoleIcon(item.role) }}
              </v-icon>
              {{ getRoleName(item.role) }}
            </v-chip>
          </template>

          <template v-slot:item.shift="{ item }">
            <v-chip color="indigo-darken-4" variant="flat" size="default" v-if="item.shift" class="elevation-2">
              <v-icon start size="16">mdi-clock-outline</v-icon>
              <span class="font-weight-medium">{{ item.shift.name }}</span>
            </v-chip>
            <v-chip color="grey-lighten-2" variant="flat" size="small" v-else>
              <span class="text-grey-darken-1">No Shift</span>
            </v-chip>
          </template>

          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-2 ">
              <v-btn 
                icon="mdi-pencil" 
                color="indigo-darken-4" 
                size="small" 
                variant="tonal" 
                @click="editItem(item)"
                class="elevation-2"
              />
              <v-btn 
                icon="mdi-delete" 
                color="red-darken-2" 
                size="small" 
                variant="tonal" 
                @click="deleteItem(item)"
                class="elevation-2"
              />
            </div>
          </template>
                    <template v-slot:body.append>
                <tr class="summary-row">
                     <td colspan="2" class="text-left font-weight-bold text-indigo-darken-4">
                       No of Records: {{filteredUsers.length }}
                    </td>
                    <td></td>
                   
                    <td></td>
                
            
                </tr>
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
  shifts: Array,
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

const getRoleColor = (role) => {
  const colors = {
    1: 'red-darken-2',
    2: 'blue-darken-2',
    3: 'purple-darken-2'
  }
  return colors[role] || 'grey'
}

const getRoleIcon = (role) => {
  const icons = {
    1: 'mdi-shield-crown',
    2: 'mdi-account',
    3: 'mdi-file-search'
  }
  return icons[role] || 'mdi-account'
}

const getRoleName = (role) => {
  const names = {
    1: 'Admin',
    2: 'Staff',
    3: 'Auditor'
  }
  return names[role] || 'Unknown'
}

const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users
  const q = searchQuery.value.toLowerCase()
  return props.users.filter(u =>
    u.name?.toLowerCase().includes(q) ||
    u.username?.toLowerCase().includes(q) ||
    u.contact?.toString().includes(q) ||
    u.shift?.name?.toLowerCase().includes(q) ||
    getRoleName(u.role).toLowerCase().includes(q)
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
.users-wrapper {
  background: linear-gradient(135deg, #e8eaf6 0%, #f5f5f5 100%);
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
  transition: all 0.3s ease;
  border: 2px solid transparent;
  background: white;
}

.stat-card:hover {
  transform: translateY(-4px);
  border-color: #3949ab;
}

.data-table-card {
  background: white;
  border: 2px solid #e8eaf6;
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


.custom-data-table {
  background: transparent;
}

.add-btn {
  text-transform: none;
  letter-spacing: 0.5px;
  font-weight: 600;
}
/* 
.card-header {
  background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
} */





@media (max-width: 600px) {
  .page-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .page-header .add-btn {
    width: 100%;
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
<template>
  <div class="template-wrapper">
    <v-container  class="py-8 px-6">
      <!-- Page Header -->
      <div class="page-header mb-8">
        <div>
          <h1 class="text-h4 font-weight-bold text-indigo-darken-4 mb-2">
            <v-icon size="32" class="mr-2">mdi-card-text-outline</v-icon>
            Card Templates
          </h1>
          <p class="text-body-1 text-medium-emphasis">
            Manage parking card pricing templates
          </p>
        </div>
        <v-btn
          color="indigo-darken-4"
          size="large"
          @click="addTemplate"
          prepend-icon="mdi-plus-circle"
          class="add-btn elevation-4"
        >
          Add Template
        </v-btn>
      </div>



      <!-- Create Dialog -->
      <Create v-model="showDialog" :selectedTemplate="selectedTemplate" />

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
              Are you sure you want to delete <strong>{{ itemToDelete?.card_name }}</strong>? This action cannot be undone.
            </v-alert>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions class="pa-4 justify-end">
            <v-btn variant="outlined" @click="showDeleteDialog = false">
              Cancel
            </v-btn>
            <v-btn color="error" variant="flat" @click="confirmDelete">
              Yes, Delete Template
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
              Templates List
            </h3>
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              label="Search templates"
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
          :items="filteredTemplates"
          class="custom-data-table"
          hide-default-footer
        >
          <template v-slot:item.id="{ item }">
            <span class="font-weight-bold text-indigo-darken-4">#{{ item.id }}</span>
          </template>

          <template v-slot:item.card_name="{ item }">
            <div class="d-flex align-center">
              <v-avatar color="indigo-lighten-5" size="40" class="mr-3">
                <v-icon color="indigo-darken-4">mdi-card-text</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-bold text-indigo-darken-4">{{ item.card_name }}</div>
                <div class="text-caption text-medium-emphasis">{{ item.no_of_days }} days card</div>
              </div>
            </div>
          </template>

          <template v-slot:item.no_of_days="{ item }">
            <v-chip color="info" variant="flat" size="small">
              <v-icon start size="12">mdi-calendar-range</v-icon>
              {{ item.no_of_days }} days
            </v-chip>
          </template>

          <template v-slot:item.price="{ item }">
            <span class="font-weight-medium">{{ formatCurrency(item.price) }}</span>
          </template>

          <template v-slot:item.discount="{ item }">
            <v-chip 
              v-if="item.discount > 0"
              color="success" 
              variant="flat" 
              size="small"
            >
              <v-icon start size="12">mdi-tag</v-icon>
              {{ formatCurrency(item.discount) }}
            </v-chip>
            <span v-else class="text-medium-emphasis">-</span>
          </template>

          <template v-slot:item.amount="{ item }">
            <span class="font-weight-bold text-indigo-darken-4 text-h6">
              {{ formatCurrency(item.amount) }}
            </span>
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
                      <template v-slot:body.append>
                <tr class="summary-row" >
                    <td colspan="2" class="text-left font-weight-bold text-indigo-darken-4">
                       No of Records: {{filteredTemplates.length }}
                    </td>
                      
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                     
                    <!-- <td class=" text-right font-weight-bold text-success">{{ formatCurrency(totalDiscount) }}</td> -->
            
                </tr>
          </template>
        </v-data-table>
      </v-card>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Create from './Create.vue'
import { usePage, router } from '@inertiajs/vue3'
import { formatCurrency } from '../../utils/utility'

const showDialog = ref(false)
const showDeleteDialog = ref(false)
const selectedTemplate = ref(null)
const itemToDelete = ref(null)
const searchQuery = ref('')

const headers = [
  { key: 'id', title: 'ID', sortable: true },
  { key: 'card_name', title: 'Card Name', sortable: true },
  { key: 'no_of_days', title: 'Duration', align: 'center' },
  { key: 'price', title: 'Price', align: 'end' },
  { key: 'discount', title: 'Discount', align: 'center' },
  { key: 'amount', title: 'Final Amount', align: 'end' },
  { key: 'actions', title: 'Actions', align: 'center', sortable: false },
]

const page = usePage()
const templates = computed(() => page.props.templates)

const filteredTemplates = computed(() => {
  if (!searchQuery.value) return templates.value

  const query = searchQuery.value.toLowerCase()
  return templates.value.filter(template => 
    template.card_name?.toLowerCase().includes(query) ||
    template.no_of_days?.toString().includes(query)
  )
})

// const totalRevenue = computed(() => {
//   return templates.value.reduce((sum, t) => sum + Number(t.amount || 0), 0)
// })

// const totalDiscount = computed(() => {
//   return templates.value.reduce((sum, t) => sum + Number(t.discount || 0), 0)
// })

// const averageDays = computed(() => {
//   if (!templates.value.length) return 0
//   const total = templates.value.reduce((sum, t) => sum + Number(t.no_of_days || 0), 0)
//   return Math.round(total / templates.value.length)
// })

const editItem = (item) => {
  selectedTemplate.value = item
  showDialog.value = true
}

const deleteItem = (item) => {
  itemToDelete.value = item
  showDeleteDialog.value = true
}

const confirmDelete = () => {
  const item = itemToDelete.value
  router.delete(route('card-template.destroy', item.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      itemToDelete.value = null
    }
  })
}

const addTemplate = () => {
  selectedTemplate.value = null
  showDialog.value = true
}
</script>

<style scoped>
.template-wrapper {
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
  border-left-color: #1a237e;
}

.stat-card:nth-child(2) {
  border-left-color: #4caf50;
}

.stat-card:nth-child(3) {
  border-left-color: #ff9800;
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
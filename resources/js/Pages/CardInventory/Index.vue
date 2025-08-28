<template>
  <v-container>
    <div class="d-flex justify-end mb-4">
      <!-- Open dialog -->
      <v-btn color="success" @click="addCard">
        Generate Card
      </v-btn>
    </div>

    <!-- Create dialog -->
    <Create v-model="showDialog" :cardTemplate = "cardTemplate" />

    <!-- Table -->
    <v-card title="Card Inventory" flat class="mt-4">
      <v-data-table
        :headers="headers"
        :items="cardDetail"
        class="elevation-1"
        hide-default-footer
      >
         <template v-slot:item.status="{ item }">
          <span class="bg-green-lighten-5 text-green-lighten-1 pa-1">{{ item.status}}</span>
         </template> 
         <template v-slot:item.created_at="{ item }">
          {{ formatDate(item.created_at) }}
        </template>
        <!-- Slot for actions -->
        <!-- <template  v-slot:item.actions="{ item }"> -->
          <!-- <v-btn
            icon="mdi-pencil"
            color="primary"
            size="small"
            @click="editItem(item)"
          ></v-btn>

          <v-btn
            icon="mdi-delete"
            color="error"
            size="small"
            @click="deleteItem(item)"
          ></v-btn> -->
        <!-- </template> -->
      </v-data-table>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref,computed} from 'vue'
import dayjs from 'dayjs'
import Create from './Create.vue'
import { usePage } from '@inertiajs/vue3'

// ✅ Dialog open/close state
const showDialog = ref(false)

// ✅ Table headers (must match your DB columns!)
const headers = [
  { key: 'id', title: 'ID' },
  { key: 'card_name', title: 'Card Name' },
  { key: 'no_of_days', title: 'No. of Days' },
  { key: 'price', title: 'price' },
  { key: 'discount', title: 'Discount' },
  { key: 'amount', title: 'Amount' },
  { key: 'balance', title: 'Balance' },
  { key: 'status', title: 'Status' },
  { key: 'created_at', title: 'Date Created' },
  // { key: 'actions', title: 'Actions' },
]

// ✅ Grab server data from Inertia props
const page = usePage()
const cardTemplate = computed(() => page.props.cardTemplate)
const cardDetail = computed(()=> page.props.cardDetail)
const selectedTemplate = ref(null)



const formatDate = (date) => {
  return date ? dayjs(date).format('MM/DD/YYYY') : ''
}

// Handlers
const editItem = (item) => {
selectedTemplate.value = item;
  showDialog.value = true;
}

const deleteItem = (item) => {
  console.log('Delete', item)
}

const addCard = () => {
 
//   selectedTemplate.value = null   // reset for "Add Template"
  showDialog.value = true
}

</script>

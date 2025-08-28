<template>
  <v-container>
    <div class="d-flex justify-end mb-4">
      <!-- Open dialog -->
      <v-btn color="success" @click="addTemplate">
        Add Template
      </v-btn>
    </div>

    <!-- Create dialog -->
    <Create v-model="showDialog" :selectedTemplate = "selectedTemplate" />

    <!-- Table -->
    <v-card title="Card Template" flat class="mt-4">
      <v-data-table
        :headers="headers"
        :items="templates"
        class="elevation-1"
        hide-default-footer
      >
        <!-- Slot for actions -->
        <template v-slot:item.actions="{ item }">
          <v-btn
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
          ></v-btn>
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref,computed } from 'vue'
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
  { key: 'actions', title: 'Actions', sortable: false },
]

// ✅ Grab server data from Inertia props
const page = usePage()
const templates = computed(()=>page.props.templates);
const selectedTemplate = ref(null)

// Handlers
const editItem = (item) => {
selectedTemplate.value = item;
  showDialog.value = true;
}

const deleteItem = (item) => {
  console.log('Delete', item)
}

const addTemplate = () => {
  selectedTemplate.value = null   // reset for "Add Template"
  showDialog.value = true
}

</script>

<template>
  <v-container>
    <!-- Add Sales Person Button -->
    <div class="d-flex justify-end mb-4">
      <v-btn color="success" @click="addSalesPerson">Add Sales Person</v-btn>
    </div>

    <!-- Create/Edit dialog -->
    <Create
      v-model="showDialog"
      :selectedItem="selectedItem"
    />

    <!-- Table -->
    <v-card title="Sales Person" class="mt-4">
      <v-data-table
        :headers="headers"
        :items="props.salesPerson.data"
        class="elevation-1"
      >
        <template v-slot:item.fullname="{ item }">
          {{ `${item.lastname} ${item.firstname}` }}
        </template>

        <template v-slot:item.actions="{ item }">
          <div class="d-flex ga-2">
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
          </div>
        </template>
      </v-data-table>

      <div class="d-flex justify-center pa-4" v-if="props.salesPerson.next_page_url">
        <v-btn color="primary" @click="loadMore">Load More</v-btn>
      </div>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Create from './Create.vue'

const props = defineProps({
  salesPerson: { type: Object, default: () => ({ data: [], next_page_url: null }) }
})

const showDialog = ref(false)
const selectedItem = ref({})

const headers = [
  { key: 'fullname', title: 'Name' },
  { key: 'address', title: 'Address' },
  { key: 'contact', title: 'Contact' },
  { key: 'actions', title: 'Action' }
]

const addSalesPerson = () => {
  selectedItem.value = {}
  showDialog.value = true
}

const editItem = (item) => {
  selectedItem.value = item
  showDialog.value = true
}

// const deleteItem = (item) => {
//   if (confirm(`Delete ${item.firstname} ${item.lastname}?`)) {
//     router.delete(route('sales-person.destroy', item.id), {
//       onSuccess: () => {
//         router.reload({ only: ['salesPerson'], preserveState: true })
//       }
//     })
//   }
// }

// Optional: load more pagination
const loadMore = () => {
  if (!props.salesPerson.next_page_url) return
  router.get(props.salesPerson.next_page_url, {}, { preserveState: true })
}
</script>

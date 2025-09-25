<template>
  <v-container>
    <!-- Add Sales Person Button -->
    <div class="d-flex justify-end mb-4">
      <v-btn color="success" @click="addusers">Add User</v-btn>
    </div>

    <!-- Create/Edit dialog -->
     <Create
      v-model="showDialog"
      :selectedItem="selectedItem"
    /> 

    <!-- Table -->
    <v-card title="Users" class="mt-4">
      <v-data-table
        :headers="headers"
        :items="props.users"
        class="elevation-1"
      >
        <template v-slot:item.fullname="{ item }">
          {{ item.name }}
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

      <div class="d-flex justify-center pa-4" v-if="props.users.next_page_url">
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
  users: { type: Object,
     default: () => (
        { data: [], next_page_url: null }
    ) 
}
})

const showDialog = ref(false)
const selectedItem = ref({})

const headers = [
  { key: 'fullname', title: 'Name' },
  { key: 'username', title: 'Username' },
  { key: 'contact', title: 'Contact' },
  { key: 'actions', title: 'Action' }
]

const addusers = () => {
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
//         router.reload({ only: ['users'], preserveState: true })
//       }
//     })
//   }
// }

// Optional: load more pagination
const loadMore = () => {
  if (!props.users.next_page_url) return
  router.get(props.users.next_page_url, {}, { preserveState: true })
}
</script>

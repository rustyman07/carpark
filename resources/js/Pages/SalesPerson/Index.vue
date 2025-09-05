<template>
  <v-container>
    <!-- Date + Type Filters -->

        <div class="d-flex justify-end mb-4">
        <!-- Open dialog -->
            <v-btn color="success" @click="addCard">
            Add Sales Person
            </v-btn>
        </div>
        {{ showDialog }}
    
    <!-- Create dialog -->
     <Create v-model="showDialog" :selectedItem = "selectedItem" />


    <!-- Table -->
    <v-card title="Sales Person"  class="mt-4">
      <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
      >
<!--       
  <template v-slot:header.fullname="{ column }">
    <div class="text-center">{{ column.title }}</div>
  </template>

  <template v-slot:header.address="{ column }">
    <div class="text-center">{{ column.title }}</div>
  </template>

  <template v-slot:header.contact="{ column }">
    <div class="text-center">{{ column.title }}</div>
  </template>

  <template v-slot:header.actions="{ column }">
    <div class="text-center">{{ column.title }}</div>
  </template> -->

        
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

            <template v-slot:item.fullname="{ item }">
            {{ `${item.lastname} ${item.firstname}` }}
            </template>
   </v-data-table >
    </v-card>
       <div class="d-flex justify-center pa-4" v-if="nextPageUrl">
        <v-btn color="primary" @click="loadMore">
          Load More
        </v-btn>
      </div>
  </v-container>
</template>

<script setup>
import { ref ,onMounted,computed} from 'vue'
import { router } from '@inertiajs/vue3'
import dayjs from "dayjs";
import Create from './Create.vue'

 const props = defineProps({
 salesPerson : {
  type: Array,   // ✅ make sure it's an array
    default: () => []
 }
 })

// onMounted(() => {
//   applyFilter()
// })

const showDialog = ref(false)
const selectedItem = ref({})


const headers = [
  { key: 'fullname', title: 'Name' },
  { key: 'address', title: 'Address' },
  { key: 'contact', title: 'Contact' },
  { key: 'actions', title: 'Action' },
 
]

const types = [
  { label: 'PARK-IN', value: 'PARK-IN' },
  { label: 'PARK-OUT', value: 'PARK-OUT' }
]


const items = ref(props.salesPerson)


const addCard = () => {
 
//   selectedTemplate.value = null   // reset for "Add Template"
  showDialog.value = true
}



const editItem = (item) => {
selectedItem.value = item;
  showDialog.value = true;
}


</script>



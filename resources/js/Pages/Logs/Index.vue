<template>
  <v-container>
    <!-- Date + Type Filters -->
    <div class="d-flex ga-4">
      <v-text-field
        type="date"
        v-model="dateFrom"
        label="From"
        variant="underlined"
        persistent-placeholder
      />

      <v-text-field
        type="date"
        v-model="dateTo"
        label="To"
        variant="underlined"
        persistent-placeholder
      />

      <v-select
        label="Select"
        :items="types"
        item-title="label"
        item-value="value"
        v-model="selectedType"
      />

      <!-- Filter Button -->
      <v-btn color="primary" @click="applyFilter">
        Search
      </v-btn>
    </div>

    <!-- Table -->
    <v-card title="Parking Logs" flat class="mt-4">
      <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
         hide-default-footer
       :items-per-page="items.length"
      />
       <div class="d-flex justify-center pa-4" v-if="nextPageUrl">
        <v-btn color="primary" @click="loadMore">
          Load More
        </v-btn>
      </div>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref ,onMounted} from 'vue'
import { router } from '@inertiajs/vue3'
import dayjs from "dayjs";

const props = defineProps({
  Tickets: Object,  // because of pagination
  filters: Object,
})

onMounted(() => {
  applyFilter()
})


const items = ref([...props.Tickets.data]); 
const nextPageUrl = ref(props.Tickets.next_page_url);


const today = new Date().toISOString().split("T")[0]
const dateFrom = ref(today)
const dateTo = ref(today)
const selectedType = ref('PARK-IN')


const headers = [
  { key: 'TICKETNO', title: 'Ticket No' },
  { key: 'PLATENO', title: 'Plate No' },
  { key: 'PARKDATETIME', title: 'Park Date/Time' },
]

const types = [
  { label: 'PARK-IN', value: 'PARK-IN' },
  { label: 'PARK-OUT', value: 'PARK-OUT' }
]

const loadMore = () => {
  if (!nextPageUrl.value) return;

  router.visit(nextPageUrl.value, {
    preserveScroll: true,
    preserveState: true,
    only: ["Tickets"], 
    onSuccess: (page) => {
      console.log("Fetched:", page.props.Tickets.data);

      // 🔹 format before appending
      const formatted = page.props.Tickets.data.map(ticket => ({
        ...ticket,
        PARKDATETIME: dayjs(ticket.PARKDATETIME).format("MM/DD/YYYY")
      }));

     items.value = [...items.value, ...formatted];
      nextPageUrl.value = page.props.Tickets.next_page_url;

      console.log("All items now:", items.value);
    },
  });
};



function applyFilter() {
  router.get('/logs', {
    dateFrom: dateFrom.value,
    dateTo: dateTo.value,
    type: selectedType.value,
  }, {
    preserveState: true,
    replace: true,
    // onSuccess: (page) => {
    //   items.value = [...page.props.Tickets.data];
    //   nextPageUrl.value = page.props.Tickets.next_page_url;
    // }

    onSuccess: (page) => {
  items.value = page.props.Tickets.data.map(ticket => ({
    ...ticket,
  PARKDATETIME: dayjs(ticket.PARKDATETIME).format("MM/DD/YYYY")
  }));
  nextPageUrl.value = page.props.Tickets.next_page_url;
}
  })
}
</script>



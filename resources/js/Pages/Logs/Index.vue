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
    <v-card title="Parking Logs"  class="mt-4">
      <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
         hide-default-footer
       :items-per-page="items.length"
      />
    
    </v-card>
       <div class="d-flex justify-center pa-4" v-if="nextPageUrl">
        <v-btn color="primary" @click="loadMore">
          Load More
        </v-btn>
      </div>
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

// onMounted(() => {
//   applyFilter()
// })


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
  { key: 'PARKOUTDATETIME', title: 'Park out Date/Time' },
  {key:'REMARKS',title: 'Remarks'}
]

const types = [
  { label: 'PARK-IN', value: 'PARK-IN' },
  { label: 'PARK-OUT', value: 'PARK-OUT' }
]


// 🔹 Shared fetcher
function fetchLogs({ url = "/logs", append = false } = {}) {
  const params = {
    dateFrom: dateFrom.value,
    dateTo: dateTo.value,
    type: selectedType.value,
  };

  router.get(
    url,
    params,
    {
      preserveState: true,
      preserveScroll: true,
      replace: !append, // replace only for filter
      only: ["Tickets"],
      onSuccess: (page) => {
        const formatted = page.props.Tickets.data.map((ticket) => ({
          ...ticket,
          PARKDATETIME: ticket.PARKDATETIME
            ? dayjs(ticket.PARKDATETIME).format("MM/DD/YYYY hh:mm A")
            : null,
          PARKOUTDATETIME: ticket.PARKOUTDATETIME
            ? dayjs(ticket.PARKOUTDATETIME).format("MM/DD/YYYY hh:mm A")
            : null,
        }));

        if (append) {
          items.value = [...items.value, ...formatted];
        } else {
          items.value = formatted;
        }

        nextPageUrl.value = page.props.Tickets.next_page_url;
      },
    }
  );
}

// 🔹 Use one function in both places
function applyFilter() {
  fetchLogs({ append: false });
}

function loadMore() {
  if (nextPageUrl.value) {
    fetchLogs({ url: nextPageUrl.value, append: true });
  }
}
</script>



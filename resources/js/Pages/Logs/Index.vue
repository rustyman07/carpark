<template>
  <v-container>
    <!-- Date + Type Filters -->
    

    <!-- Table -->
    <v-card title="Parking Logs"  class="mt-4">
      <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
         hide-default-footer
       :items-per-page="items.length"
      >
    
   <template v-slot:top>

        <v-row class="pa-2" justify="end">
          <v-col cols="12" sm="4" md="2">
            <v-date-input
             v-model="dateFrom"
             prepend-icon=""
            label="From"
            prepend-inner-icon="$calendar"
            density="compact"
            hideDetails="auto"
            variant="underlined"
            />
        </v-col>
      <v-col cols="12" sm="4" md="2">
       <v-date-input
             v-model="dateTo"
             prepend-icon=""
            label="To"
            prepend-inner-icon="$calendar"
            density="compact"
            hideDetails="auto"
            variant="underlined"
            />
    </v-col>
       <v-col cols="12" sm="4" md="2">
      <v-select
        label="Select"
        :items="types"
        item-title="label"
        item-value="value"
        v-model="selectedType"
        density="compact"
         hideDetails="auto"
        variant="underlined"
      />
    </v-col>
         <v-col cols="12" sm="4" md="1">
      <!-- Filter Button -->
      <v-btn  size ="small"color="primary" @click="applyFilter">
        Search
      </v-btn>
        </v-col>

    </v-row>

    

    
   </template>






      <template v-slot:item.ACTION = "{ item}">
        <v-btn 
        color="error"
       @click=deleteTicket(item.id)
        >Void</v-btn>
      </template>

      <template v-slot:item.PARKOUTDATETIME = "{ item}">
           {{ item.PARKOUTDATETIME ? item.PARKOUTDATETIME : '-' }}
      </template>


      
      
      </v-data-table>

   

    
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
import { formatDate } from '../../utils/utility';

const props = defineProps({
  Tickets: Object,  // because of pagination
  filters: Object,
})

// onMounted(() => {
//   applyFilter()
// })


const items = ref(formatTickets(props.Tickets.data));
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
  {key: 'REMARKS', title:'Remarks'},
  { key: 'ACTION', title: 'Action'}

]

const types = [
  { label: 'PARK-IN', value: 'PARK-IN' },
  { label: 'PARK-OUT', value: 'PARK-OUT' }
]


function formatTickets(tickets) {
  return tickets.map((ticket) => ({
    ...ticket,
    PARKDATETIME: formatDate(ticket.PARKDATETIME),
    PARKOUTDATETIME:formatDate(ticket.PARKOUTDATETIME)
  }));
}



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
        const formatted = formatTickets(page.props.Tickets.data);
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
    fetchLogs({ url: nextPageUrl.value ,append: true ,});
  }
}


const deleteTicket = (id) => {
  if (confirm('Are you sure you want to void this ticket?')) {
    console.log('Voiding ticket with ID:', id)

    items.value = items.value.filter(ticket => ticket.id !== id)

    router.delete(route('logs.delete', { id }), {
      onSuccess: () => {
        console.log('Ticket has been voided.')
        fetchLogs({ append: false }) 
      },
      onError: (errors) => {
        console.error(errors)


        fetchLogs({ append: false })
      }
    })
  }
}




</script>



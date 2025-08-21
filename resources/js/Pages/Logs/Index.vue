<template>
  <v-container>
    <!-- Date Picker -->
    <div class="d-flex ga-4">
      <v-text-field
        type="date"
        v-model="dateFrom"
        label="From"
        variant="underlined"
        persistent-placeholder
      ></v-text-field>

      <v-text-field
        type="date"
        v-model="dateTo"
        label="To"
        variant="underlined"
        persistent-placeholder
      ></v-text-field>

      <v-select
        label="Select"
        :items="types"
        item-title="label"
        item-value="value"
        v-model="selectedType"
      />

      <v-select
        label="Shift"
        :items="shifts"
        item-title="label"
        item-value="value"
        v-model="selectedShift"
      />
    </div>

    <!-- Table -->
    <v-card title="Parking Logs" flat class="mt-4">
      <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
      />
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  Tickets: {
    type: Array,
    default: () => [],
  }
})

const dateFrom = ref(new Date().toISOString().split("T")[0])
const dateTo = ref(new Date().toISOString().split("T")[0])

const selectedType = ref(1)
const selectedShift = ref(1)

const headers = [
  { key: 'TICKETNO', title: 'Ticket No' },
  { key: 'PLATENO', title: 'Plate No' },
  { key: 'PARKDATETIME', title: 'Park Date/Time' },
]

const types = [
  { label: "PARK-IN", value: 1 },
  { label: "PARK-OUT", value: 2 }
]

const shifts = [
  { label: "6AM-2PM", value: 1 },
  { label: "2PM-10PM", value: 2 },
  { label: "10PM-6AM", value: 3 },
  { label: "6AM-6PM", value: 4 },
  { label: "6PM-6AM", value: 5 }
]

// Computed items that automatically update when filters change
const items = computed(() => {
  return props.Tickets
    .filter(t => {
      const parkDate = new Date(t.PARKDATETIME).toISOString().split("T")[0]
      return parkDate >= dateFrom.value &&
             parkDate <= dateTo.value 
            //  t.TYPE === selectedType.value &&
            //  t.SHIFT === selectedShift.value
    })
    .map(t => ({
      TICKETNO: t.TICKETNO,
      PLATENO: t.PLATENO,
      PARKDATETIME: new Date(t.PARKDATETIME).toLocaleString()
    }))
})
</script>

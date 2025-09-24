<template>
  <v-container>
    <!-- <div class="d-flex justify-end mb-4">

      <v-btn color="success" @click="addPayment">
        Add Payment
      </v-btn>
    </div> -->

    <!-- Table -->
    <v-card title="Ticket Payments" class="mt-4">
      <v-data-table-server
        :headers="headers"
        :items="payments.data"
        v-model:items-per-page="itemsPerPage"
        :page="pageNumber"
        :items-length="payments.total"
      >
        <template v-slot:item.amount="{ item }">
          {{ formatCurrency(item.amount) }}
        </template>

        <template v-slot:item.days_deducted="{ item }">
          {{ item.days_deducted || '-' }}
        </template>

        <template v-slot:item.payment_method="{ item }">
          <span :class="item.payment_method === 'card' ? 'bg-blue-lighten-5 text-blue-lighten-1 pa-1' : 'bg-green-lighten-5 text-green-lighten-1 pa-1'">
            {{ item.payment_method }}
          </span>
        </template>

        <template v-slot:item.card_name="{ item }">
          {{ item.card ? item.card.card_name : '-' }}
        </template>

        <template v-slot:item.paid_at="{ item }">
          {{ formatDate(item.paid_at) }}
        </template>

        <template v-slot:item.status="{ item }">
          <span :class="item.status === 'paid' ? 'bg-green-lighten-5 text-green-lighten-1 pa-1' : 'bg-red-lighten-5 text-red-lighten-1 pa-1'">
            {{ item.status }}
          </span>
        </template>

      </v-data-table-server>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'
import dayjs from 'dayjs'
import { usePage } from '@inertiajs/vue3'
import { formatCurrency,formatDate } from '../../utils/utility'

// Table headers
const headers = [
  { key: 'id', title: 'ID' },
//   { key: 'card_name', title: 'Card Name' },
  { key: 'amount', title: 'Cash Amount' },
{ key: 'total_amount', title: 'Total Amount' },

//   { key: 'days_deducted', title: 'Days Deducted' },
//   { key: 'payment_method', title: 'Payment Method' },
   { key: 'change', title: 'Change' },
      { key: 'payment_type', title: 'Payment Type' },
  { key: 'status', title: 'Status' },
  { key: 'paid_at', title: 'Paid At' },
]

// Pull payments from Inertia page props
const page = usePage()
const payments = computed(() => page.props.payments) // should be array of Payment models


// Optional currency formatter

// Optional add payment handler
const addPayment = () => {
  console.log('Open add payment dialog')
}
</script>

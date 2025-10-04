<template>
  <v-dialog 
    v-model="dialog" 
    min-height="500"
    max-width="370"
    
  >
    <v-card class="rounded">
<v-toolbar flat density="compact" class="bg-indigo-darken-4">
  <v-toolbar-title  >
    <span class="text-base text-white"> Card: {{ props.selectedCard.card_number }}</span>
  </v-toolbar-title>
  <v-spacer></v-spacer>
  <v-btn icon="mdi-close" variant="text" @click="dialog = false"></v-btn>
</v-toolbar>
      <v-divider></v-divider>

      <v-card-text class="py-4 bg-gray-100">
        <div v-if="loading" class="text-center py-12">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </div>

        <div v-else-if="error" class="text-center text-error py-12">
          <v-icon class="mb-2">mdi-alert-circle-outline</v-icon>
          <p>{{ error }}</p>
        </div>
        
        <div v-else-if="Object.keys(groupedTransactions).length === 0" class="text-center py-12">
          <v-icon class="mb-4" size="64" color="grey-lighten-2">mdi-cash-remove</v-icon>
          <p class="text-subtitle-1 text-medium-emphasis">No transactions available.</p>
          <p class="text-caption text-medium-emphasis">There are no records for this card yet.</p>
        </div>

        <div v-else>
            <div class="flex justify-center ">
              <div class="flex flex-col text-center mb-4 ">
                  <span class="text-xs md:text-sm ">Currrent Balance</span>
                  <span class="text-base md:text-3xl font-bold text-indigo-darken-4">{{ props.selectedCard.balance }}</span>
              </div>
            </div>
          <div v-for="(dayTransactions, date) in groupedTransactions" :key="date">
            <div class="text-xs md:text-sm mb-1 ">
              {{ date }}
            </div>

            <div class =" mb-2  bg-white flex justify-between text-xs md:text-sm  shadow-md rounded-md p-2" v-for="t in dayTransactions" >
               <div class="flex p-1  gap-2">
                            <v-avatar color="grey-lighten-4" size="40">
                                <v-icon color="indigo-darken-4" size="24">mdi-car</v-icon>
                            </v-avatar>
                        <div class="flex flex-col flex-1">
                            <div>
                                <span class="text-gray-500">Plate No: </span>
                                <span class="text-indigo-darken-4 font-bold">{{t.payment?.ticket?.plate_no  }}</span>
                            </div>
                        <div>
                            <span class="text-gray-500">Ticket No:</span>
                            <span class="text-gray-500">{{t.payment?.ticket?.ticket_no  }}</span>
                        </div>

                        </div>
                    

                </div>
                        <div class="flex flex-col justify-center">
                            <span class=" text-sm text-red-600 font-semibold">-{{ t.amount }}</span>
                        </div>
      
            </div>

            <!-- <v-list class="bg-surface rounded-lg">
              <v-list-item
                v-for="t in dayTransactions"
                :key="t.id"
              >
                <template #prepend>
                  <v-avatar color="grey-lighten-4" size="40">
                    <v-icon color="grey-darken-1" size="24">mdi-car</v-icon>
                  </v-avatar>
                </template>

                <v-list-item-title class="font-weight-medium text-body-2">
                  <span class="text-medium-emphasis">Plate No:</span>
                  <span class="text-primary font-weight-regular ms-1">{{ t.payment?.ticket?.plate_no }}</span>
                </v-list-item-title>

                <v-list-item-subtitle class="text-caption mt-1 text-medium-emphasis">
                  Amount: <span class="font-weight-bold text-success">₱{{ t.amount }}</span> 
                  <span class="mx-1">·</span>
                  Status:
                  <v-chip
                    :color="t.payment?.status === 'Paid' ? 'green-darken-2' : 'orange-darken-2'"
                    size="x-small"
                    label
                    class="ms-1"
                  >
                    {{ t.payment?.status }}
                  </v-chip>
                </v-list-item-subtitle>
              </v-list-item>
            </v-list> -->
          </div>
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import { route } from 'ziggy-js';
import { WordformatDate } from '../../utils/utility';

const props = defineProps({
  selectedCard: Object
})

const dialog = defineModel({ type: Boolean, default: false })

const transactions = ref([])
const loading = ref(false)
const error = ref(null)

// Group by day
const groupedTransactions = computed(() => {
  return transactions.value.reduce((acc, t) => {
    const day = WordformatDate(t.created_at, "MMMM DD, YYYY")
    if (!acc[day]) acc[day] = []
    acc[day].push(t)
    return acc
  }, {})
})

onMounted(async () => {
  loading.value = true
  try {
    const { data } = await axios.get(route('transactions', props.selectedCard.id))
    transactions.value = data.transactions
  } catch (err) {
    console.error('❌ Fetch error:', err)
    error.value = err.response?.data?.message || 'Failed to load transactions'
  } finally {
    loading.value = false
  }
})
</script>
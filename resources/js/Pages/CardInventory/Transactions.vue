<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card class="rounded-lg">
      <!-- Header -->
      <v-card-title    class="d-flex align-center justify-space-between text-white py-1 px-4 bg-blue-darken-4">
        <span class="text-h6">🚗 Transactions for Card: {{ props.card_id }}</span>
        <v-btn icon="mdi-close" variant="text" @click="dialog = false" color="white"></v-btn>
      </v-card-title>

      <v-divider></v-divider>

      <!-- Body -->
      <v-card-text>
        <div v-if="loading" class="text-center py-6">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </div>

        <div v-else-if="error" class="text-center text-error py-6">
          {{ error }}
        </div>

        <div v-else>
          <!-- Loop by day -->
          <v-card
            v-for="(dayTransactions, date) in groupedTransactions"
            :key="date"
            class="mb-6 rounded-lg"
            elevation="4"
          >
            <!-- Date Header -->
            <v-card-title class="bg-grey-lighten-4 text-subtitle-1 font-weight-bold">
              📅 {{ date }}
            </v-card-title>

            <v-divider></v-divider>

            <!-- Transactions list -->
            <v-list density="compact">
              <v-list-item
                v-for="t in dayTransactions"
                :key="t.id"
                class="py-3"
              >
                <template #prepend>
                  <v-avatar color="blue-lighten-4" size="40">
                    <v-icon color="blue">mdi-car</v-icon>
                  </v-avatar>
                </template>

                <v-list-item-content>
                  <v-list-item-title class="font-weight-medium">
                    Plate No: <span class="text-primary">{{ t.payment?.ticket?.PLATENO }}</span>
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    Amount: <strong class="text-success">₱{{ t.amount }}</strong> |
                    Status: 
                    <v-chip
                      :color="t.payment?.status === 'paid' ? 'green' : 'orange'"
                      size="small"
                      label
                    >
                      {{ t.payment?.status }}
                    </v-chip>
                  </v-list-item-subtitle>
                </v-list-item-content>

                <template #append>
                  <v-btn size="small" icon="mdi-information" variant="text" color="primary"></v-btn>
                </template>
              </v-list-item>
            </v-list>
          </v-card>
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
  card_id: Number
})

const transactions = ref([])
const loading = ref(false)
const error = ref(null)

// Group by day
const groupedTransactions = computed(() => {
  return transactions.value.reduce((acc, t) => {
    const day = WordformatDate(t.created_at, "MMMM DD, YYYY") // e.g. "September 21, 2025"
    if (!acc[day]) acc[day] = []
    acc[day].push(t)
    return acc
  }, {})
})

onMounted(async () => {
  loading.value = true
  try {
    const { data } = await axios.get(route('transactions', props.card_id)) 
    transactions.value = data.transactions
    console.log('✅ Transactions fetched', transactions.value)
  } catch (err) {
    console.error('❌ Fetch error:', err)
    error.value = err.response?.data?.message || 'Failed to load transactions'
  } finally {
    loading.value = false
  }
})
</script>

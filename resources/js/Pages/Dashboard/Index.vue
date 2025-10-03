<template>
  <v-container class="py-8">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <v-card class="p-6 text-center shadow-lg rounded-2xl">
        <v-icon size="40" color="blue">mdi-car</v-icon>
        <h2 class="text-2xl font-bold mt-2">{{ activeParkings }}</h2>
        <p class="text-gray-600">Active Parkings</p>
      </v-card>

      <v-card class="p-6 text-center shadow-lg rounded-2xl">
        <v-icon size="40" color="green">mdi-card-account-details</v-icon>
        <h2 class="text-2xl font-bold mt-2">{{ totalCards }}</h2>
        <p class="text-gray-600">Total Cards Issued</p>
      </v-card>

      <v-card class="p-6 text-center shadow-lg rounded-2xl">
        <v-icon size="40" color="amber">mdi-cash</v-icon>
        <h2 class="text-2xl font-bold mt-2">₱{{ totalRevenue.toLocaleString() }}</h2>
        <p class="text-gray-600">Total Revenue Today</p>
      </v-card>
    </div>

    <!-- Revenue Chart -->
    <v-card class="p-6 shadow-lg rounded-2xl">
      <h3 class="text-xl font-semibold mb-4">Daily Revenue (Last 7 Days)</h3>
      <apexchart type="bar" height="300" :options="chartOptions" :series="series" />
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import ApexCharts from 'vue3-apexcharts';

const props = defineProps({
  activeParkings: Number,
  totalCards: Number,
  totalRevenue: Number,
  revenueData: Array,
});

const activeParkings = ref(props.activeParkings);
const totalCards = ref(props.totalCards);
const totalRevenue = ref(props.totalRevenue);

// Convert Laravel revenue data into chart series
const categories = props.revenueData.map(item => item.date);
const values = props.revenueData.map(item => item.total);

const series = ref([
  {
    name: 'Revenue',
    data: values,
  },
]);

const chartOptions = ref({
  chart: {
    toolbar: { show: false },
  },
  colors: ['#3B82F6'],
  dataLabels: { enabled: false },
  xaxis: {
    categories: categories,
    labels: { style: { colors: '#6B7280' } },
  },
  yaxis: {
    labels: { formatter: (val) => '₱' + val.toLocaleString() },
  },
  tooltip: {
    y: { formatter: (val) => '₱' + val.toLocaleString() },
  },
});
</script>

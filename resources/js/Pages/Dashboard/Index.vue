<template>
  <div class="dashboard-wrapper">
    <v-container class="py-8 px-6">
      <!-- Dashboard Header -->
      <div class="dashboard-header mb-8">
        <div>
          <h1 class="text-h4 font-weight-bold text-indigo-darken-4 mb-2">
            Dashboard Overview
          </h1>
          <p class="text-indigo-darken-4 text-body-1">
            <v-icon size="small" class="mr-1">mdi-calendar-today</v-icon>
            {{ getCurrentDate() }}
          </p>
        </div>
        <v-chip color="white" variant="flat" size="large" class="px-4">
          <v-icon start>mdi-clock-outline</v-icon>
          {{ getCurrentTime() }}
        </v-chip>
      </div>

      <!-- KPI Cards -->
      <v-row class="mb-8">
        <!-- Active Parkings -->
        <v-col cols="12" md="4">
          <v-card 
            class="kpi-card elevation-8" 
            rounded="xl"
            :ripple="false"
          >
            <div class="kpi-card-content pa-6">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="kpi-icon-wrapper blue-gradient">
                  <v-icon size="32" color="white">mdi-car-multiple</v-icon>
                </div>
                <v-chip size="small" color="success" variant="flat">
                  <v-icon start size="12">mdi-circle</v-icon>
                  Live
                </v-chip>
              </div>
              
              <div class="kpi-value-section">
                <h2 class="kpi-value text-indigo-darken-4 mb-1">
                  {{ activeParkings }}
                </h2>
                <p class="kpi-label text-medium-emphasis mb-3">Active Parkings</p>
                <div class="d-flex align-center">
                  <v-icon size="16" color="success" class="mr-1">mdi-trending-up</v-icon>
                  <span class="text-caption text-success font-weight-medium">12% from yesterday</span>
                </div>
              </div>
            </div>
            <div class="kpi-card-footer">
              <v-btn 
                variant="text" 
                color="indigo-darken-4" 
                size="small"
                append-icon="mdi-arrow-right"
              >
                View Details
              </v-btn>
            </div>
          </v-card>
        </v-col>

        <!-- Total Cards -->
        <v-col cols="12" md="4">
          <v-card 
            class="kpi-card elevation-8" 
            rounded="xl"
            :ripple="false"
          >
            <div class="kpi-card-content pa-6">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="kpi-icon-wrapper green-gradient">
                  <v-icon size="32" color="white">mdi-card-account-details</v-icon>
                </div>
                <v-chip size="small" color="info" variant="flat">
                  <v-icon start size="12">mdi-information</v-icon>
                  Total
                </v-chip>
              </div>
              
              <div class="kpi-value-section">
                <h2 class="kpi-value text-indigo-darken-4 mb-1">
                  {{ totalCards.toLocaleString() }}
                </h2>
                <p class="kpi-label text-medium-emphasis mb-3">Cards Issued</p>
                <div class="d-flex align-center">
                  <v-icon size="16" color="info" class="mr-1">mdi-plus-circle</v-icon>
                  <span class="text-caption text-info font-weight-medium">{{ newCardsToday }} new today</span>
                </div>
              </div>
            </div>
            <div class="kpi-card-footer">
              <v-btn 
                variant="text" 
                color="indigo-darken-4" 
                size="small"
                append-icon="mdi-arrow-right"
              >
                Manage Cards
              </v-btn>
            </div>
          </v-card>
        </v-col>

        <!-- Total Revenue -->
        <v-col cols="12" md="4">
          <v-card 
            class="kpi-card elevation-8" 
            rounded="xl"
            :ripple="false"
          >
            <div class="kpi-card-content pa-6">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="kpi-icon-wrapper amber-gradient">
                  <v-icon size="32" color="white">mdi-cash-multiple</v-icon>
                </div>
                <v-chip size="small" color="success" variant="flat">
                  <v-icon start size="12">mdi-trending-up</v-icon>
                  +8.2%
                </v-chip>
              </div>
              
              <div class="kpi-value-section">
                <h2 class="kpi-value text-indigo-darken-4 mb-1">
                  ₱{{ totalRevenue.toLocaleString() }}
                </h2>
                <p class="kpi-label text-medium-emphasis mb-3">Revenue Today</p>
                <div class="d-flex align-center">
                  <v-icon size="16" color="success" class="mr-1">mdi-arrow-up-bold</v-icon>
                  <span class="text-caption text-success font-weight-medium">₱{{ (totalRevenue * 0.082).toFixed(2) }} vs yesterday</span>
                </div>
              </div>
            </div>
            <div class="kpi-card-footer">
              <v-btn 
                variant="text" 
                color="indigo-darken-4" 
                size="small"
                append-icon="mdi-arrow-right"
              >
                View Report
              </v-btn>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Charts Row -->
      <v-row>
        <!-- Revenue Chart -->
        <v-col cols="12" lg="8">
          <v-card class="chart-card elevation-8" rounded="xl">
            <div class="chart-header pa-6 pb-4">
              <div class="d-flex justify-space-between align-center">
                <div>
                  <h3 class="text-h6 font-weight-bold text-indigo-darken-4 mb-1">
                    Revenue Analytics
                  </h3>
                  <p class="text-caption text-medium-emphasis">
                    Daily revenue performance over the last 7 days
                  </p>
                </div>
                <v-btn-toggle
                  v-model="chartView"
                  color="indigo-darken-4"
                  variant="outlined"
                  divided
                  density="compact"
                  mandatory
                >
                  <v-btn value="bar" size="small">
                    <v-icon>mdi-chart-bar</v-icon>
                  </v-btn>
                  <v-btn value="line" size="small">
                    <v-icon>mdi-chart-line</v-icon>
                  </v-btn>
                  <v-btn value="area" size="small">
                    <v-icon>mdi-chart-areaspline</v-icon>
                  </v-btn>
                </v-btn-toggle>
              </div>
            </div>
            <v-divider></v-divider>
            <div class="pa-6 pt-4">
              <apexchart 
                :type="chartView" 
                height="320" 
                :options="chartOptions" 
                :series="series" 
              />
            </div>
          </v-card>
        </v-col>

        <!-- Statistics Summary -->
        <v-col cols="12" lg="4">
          <v-card class="stats-card elevation-8" rounded="xl">
            <div class="pa-6">
              <h3 class="text-h6 font-weight-bold text-indigo-darken-4 mb-1">
                Quick Statistics
              </h3>
              <p class="text-caption text-medium-emphasis mb-6">
                Summary of today's activities
              </p>

              <!-- Stat Items -->
              <div class="stat-item mb-5">
                <div class="d-flex align-center justify-space-between mb-2">
                  <span class="text-body-2 text-medium-emphasis">Average Duration</span>
                  <span class="text-subtitle-2 font-weight-bold text-indigo-darken-4">3.5 hrs</span>
                </div>
                <v-progress-linear
                  model-value="70"
                  color="indigo-darken-4"
                  height="8"
                  rounded
                ></v-progress-linear>
              </div>

              <div class="stat-item mb-5">
                <div class="d-flex align-center justify-space-between mb-2">
                  <span class="text-body-2 text-medium-emphasis">Occupancy Rate</span>
                  <span class="text-subtitle-2 font-weight-bold text-indigo-darken-4">85%</span>
                </div>
                <v-progress-linear
                  model-value="85"
                  color="success"
                  height="8"
                  rounded
                ></v-progress-linear>
              </div>

              <div class="stat-item mb-5">
                <div class="d-flex align-center justify-space-between mb-2">
                  <span class="text-body-2 text-medium-emphasis">Peak Hours</span>
                  <span class="text-subtitle-2 font-weight-bold text-indigo-darken-4">2-5 PM</span>
                </div>
                <v-progress-linear
                  model-value="95"
                  color="warning"
                  height="8"
                  rounded
                ></v-progress-linear>
              </div>

              <v-divider class="my-5"></v-divider>

              <!-- Recent Activity -->
              <h4 class="text-subtitle-2 font-weight-bold text-indigo-darken-4 mb-4">
                Recent Activity
              </h4>
              
              <div class="activity-list">
                <div class="activity-item mb-4">
                    <v-avatar size="36" color="success" class="mr-3">
                    <v-icon size="20" color="white">mdi-car-arrow-right</v-icon>
                  </v-avatar>
                  <div class="flex-grow-1">
                    <p class="text-body-2 font-weight-medium mb-0">Vehicle checked in</p>
<p class="text-caption text-medium-emphasis">
  {{ props.latestParkin.plate_no     }} • {{ timeAgo(props.latestParkin.park_datetime) }}
</p>
                  </div>
                </div>

                <div class="activity-item mb-4">
                  <v-avatar size="36" color="error" class="mr-3">
                    <v-icon size="20" color="white">mdi-car-arrow-left</v-icon>
                  </v-avatar>
                  <div class="flex-grow-1">
                    <p class="text-body-2 font-weight-medium mb-0">Vehicle checked out</p>
                    <p class="text-caption text-medium-emphasis">{{ props.latestParkout.plate_no     }} • {{ timeAgo(props.latestParkout.park_out_datetime) }}</p>
                  </div>
                </div>

                <div class="activity-item">
                  <v-avatar size="36" color="info" class="mr-3">
                    <v-icon size="20" color="white">mdi-card-plus</v-icon>
                  </v-avatar>
                  <div class="flex-grow-1">
                    <p class="text-body-2 font-weight-medium mb-0">New card issued</p>
                    <p class="text-caption text-medium-emphasis">Card #1234 • 15 mins ago</p>
                  </div>
                </div>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import ApexCharts from 'vue3-apexcharts';
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";


const props = defineProps({
  activeParkings: Number,
  totalCards: Number,
  totalRevenue: Number,
  revenueData: Array,
  latestParkin: Object,
  latestParkout: Object
});



dayjs.extend(relativeTime);

const timeAgo = (datetime) => {
  return dayjs(datetime).fromNow();
};

const activeParkings = ref(props.activeParkings);
const totalCards = ref(props.totalCards);
const totalRevenue = ref(props.totalRevenue);
const newCardsToday = ref(Math.floor(props.totalCards * 0.05)); // Example calculation
const chartView = ref('bar');
const currentTime = ref('');

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
    fontFamily: 'Roboto, sans-serif',
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800,
    },
  },
  colors: ['#1A237E'],
  plotOptions: {
    bar: {
      borderRadius: 8,
      columnWidth: '60%',
      dataLabels: {
        position: 'top',
      },
    },
  },
  dataLabels: { 
    enabled: false,
  },
  stroke: {
    curve: 'smooth',
    width: 3,
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: 'vertical',
      shadeIntensity: 0.25,
      opacityFrom: 0.85,
      opacityTo: 0.55,
      stops: [0, 100],
    },
  },
  grid: {
    borderColor: '#E0E0E0',
    strokeDashArray: 4,
    xaxis: {
      lines: { show: false },
    },
    yaxis: {
      lines: { show: true },
    },
    padding: {
      top: 0,
      right: 20,
      bottom: 0,
      left: 10,
    },
  },
  xaxis: {
    categories: categories,
    labels: { 
      style: { 
        colors: '#757575',
        fontSize: '12px',
        fontWeight: 500,
      } 
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    labels: { 
      formatter: (val) => '₱' + val.toLocaleString(),
      style: { 
        colors: '#757575',
        fontSize: '12px',
        fontWeight: 500,
      },
    },
  },
  tooltip: {
    theme: 'light',
    y: { 
      formatter: (val) => '₱' + val.toLocaleString(),
    },
    style: {
      fontSize: '12px',
    },
  },
});

// Get current date
const getCurrentDate = () => {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  return new Date().toLocaleDateString('en-US', options);
};

// Get current time
const getCurrentTime = () => {
  const now = new Date();
  return now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

// Update time every minute
let timeInterval;
onMounted(() => {
  currentTime.value = getCurrentTime();
  timeInterval = setInterval(() => {
    currentTime.value = getCurrentTime();
  }, 60000);
});

onUnmounted(() => {
  if (timeInterval) clearInterval(timeInterval);
});
</script>

<style scoped>
.dashboard-wrapper {
  /* background: linear-gradient(135deg, #1A237E 0%, #283593 50%, #3949AB 100%); */
  min-height: 100vh;
  position: relative;
}

.dashboard-wrapper::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 300px;
  background: radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  pointer-events: none;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.text-white-70 {
  color: rgba(255, 255, 255, 0.7);
}

/* KPI Cards */
.kpi-card {
  background: white;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.kpi-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(26, 35, 126, 0.15) !important;
}

.kpi-card-content {
  position: relative;
}

.kpi-icon-wrapper {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.blue-gradient {
  background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
}

.green-gradient {
  background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
}

.amber-gradient {
  background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%);
}

.kpi-value {
  font-size: 2.5rem;
  font-weight: 700;
  line-height: 1;
}

.kpi-label {
  font-size: 0.875rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.kpi-card-footer {
  padding: 12px 24px;
  background: rgba(26, 35, 126, 0.02);
  border-top: 1px solid rgba(0, 0, 0, 0.05);
}

/* Chart Card */
.chart-card {
  background: white;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.chart-header {
  background: rgba(26, 35, 126, 0.02);
}

/* Stats Card */
.stats-card {
  background: white;
  border: 1px solid rgba(0, 0, 0, 0.05);
  height: 100%;
}

.stat-item {
  transition: all 0.2s ease;
}

.activity-list {
  max-height: 280px;
  overflow-y: auto;
}

.activity-item {
  display: flex;
  align-items: center;
  padding: 8px;
  border-radius: 8px;
  transition: background 0.2s ease;
}

.activity-item:hover {
  background: rgba(26, 35, 126, 0.04);
}

/* Scrollbar Styling */
.activity-list::-webkit-scrollbar {
  width: 6px;
}

.activity-list::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.activity-list::-webkit-scrollbar-thumb {
  background: #1A237E;
  border-radius: 10px;
}

.activity-list::-webkit-scrollbar-thumb:hover {
  background: #283593;
}

/* Responsive Design */
@media (max-width: 960px) {
  .kpi-value {
    font-size: 2rem;
  }
  
  .dashboard-header {
    text-align: center;
  }
}

/* Animation */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.v-card {
  animation: fadeInUp 0.6s ease-out;
}
</style>
<template>
 
    <v-container fluid class="fill-height">
      <v-row justify="center" align="center" class="fill-height">
        <v-col cols="12" md="8" lg="6">
          <!-- Main Entry Card -->
         
        
            
            <!-- Clock Section -->
            <div class="clock-section pa-8 text-center">
              <!-- <v-chip 
                color="indigo-darken-4" 
                variant="flat" 
                size="large"
                class="mb-4 status-chip"
              >
                <v-icon start>mdi-clock-check-outline</v-icon>
                SYSTEM READY
              </v-chip> -->

              <div class="date-display mb-2">
                <v-icon size="24" class="mr-2">mdi-calendar-today</v-icon>
                {{ date }}
              </div>
              
              <div class="time-display mb-4">
                <span class="time-value">{{ time }}</span>
                <span class="time-period">{{ AMPM }}</span>
              </div>

              <v-divider class="my-6"></v-divider>

              <!-- Entry Instructions -->
              <div class="instructions-section mb-6">
                <h3 class="text-h6 font-weight-bold text-indigo-darken-4 mb-2">
                  <v-icon class="mr-2">mdi-information-outline</v-icon>
                  Vehicle Entry
                </h3>
                <p class="text-body-2 text-medium-emphasis">
                  Enter the vehicle plate number to generate a parking ticket
                </p>
              </div>

              <!-- Plate Number Input -->
              <div class="input-section mb-6">
                <v-text-field
                  v-model="form.plate_no"
                  placeholder="ABC1234"
                  variant="outlined"
                  :error-messages="form.errors.plate_no"
                  @input="form.plate_no = form.plate_no.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()"
                  class="plate-input"
                  density="comfortable"
                  autofocus
                >
                  <template v-slot:prepend-inner>
                    <v-icon color="indigo-darken-4">mdi-car</v-icon>
                  </template>
                  <template v-slot:append-inner>
                    <v-fade-transition>
                      <v-icon 
                        v-if="form.plate_no && !form.errors.plate_no" 
                        color="success"
                      >
                        mdi-check-circle
                      </v-icon>
                    </v-fade-transition>
                  </template>
                </v-text-field>

                <!-- Quick Input Hint -->
                <div class="text-caption text-medium-emphasis text-center mt-2">
                  <v-icon size="14" class="mr-1">mdi-lightbulb-outline</v-icon>
                  Letters and numbers only (e.g., ABC1234)
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="action-buttons">
                <v-btn
                  size="x-large"
                  @click="createTicket"
                  block
                  color="indigo-darken-4"
                  :disabled="form.processing || !form.plate_no"
                  :loading="form.processing"
                  class="mb-4 action-btn"
                  height="64"
                >
                  <v-icon start size="28">mdi-ticket-confirmation</v-icon>
                  <span class="text-h6 font-weight-bold">Generate Ticket</span>
                </v-btn>

              </div>
            </div>

        </v-col>
      </v-row>
    </v-container>

    <!-- Success Snackbar -->
    <v-snackbar
      v-model="showSuccessSnackbar"
      color="success"
      location="top"
      timeout="3000"
    >
      <div class="d-flex align-center">
        <v-icon class="mr-2">mdi-check-circle</v-icon>
        <span class="font-weight-medium">Ticket generated successfully!</span>
      </div>
    </v-snackbar>

</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import dayjs from 'dayjs';
import { useForm } from '@inertiajs/vue3';

// Refs for live clock and plate number
const time = ref('');
const date = ref('');
const AMPM = ref('');
const showSuccessSnackbar = ref(false);

// Interval ID for clearing on unmount
let intervalId;

// Function to update time and date every second
const updateTime = () => {
  const now = dayjs();
  time.value = now.format('hh:mm:ss');
  date.value = now.format('MMMM D, YYYY');
  AMPM.value = now.format('A');
};

// Start live clock on mount
onMounted(() => {
  updateTime(); // initial call
  intervalId = setInterval(updateTime, 1000); // update every second
});

// Clear interval on unmount
onUnmounted(() => {
  clearInterval(intervalId);
});

// Use useForm to manage form state and submission
const form = useForm({
  plate_no: '',
  park_year: '',
  park_month: '',
  park_day: '',
  park_hour: '',
  park_minute: '',
  park_second: '',
});

// Function to create ticket
const createTicket = () => {
  // Get current timestamp and populate form fields
  const now = dayjs();
  form.park_year = now.year();
  form.park_month = now.month() + 1;
  form.park_day = now.date();
  form.park_hour = now.hour();
  form.park_minute = now.minute();
  form.park_second = now.second();

  // Submit the form
  form.post(route('parkin.store'), {
    onSuccess: () => {
      // Show success message
      showSuccessSnackbar.value = true;
      // Reset the plate number input after a successful submission
      form.reset('plate_no');
    },
    onError: () => {
      // Errors are automatically populated in form.errors
    }
  });
};
</script>

<style scoped>
.parking-entry-wrapper {
  /* background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%); */
  min-height: 100vh;
  position: relative;
  overflow: hidden;
}

.parking-entry-wrapper::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
  pointer-events: none;
}

/* Main Entry Card */
.entry-card {
  background: white;
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(26, 35, 126, 0.1);
  animation: fadeInScale 0.6s ease-out;
}

.card-glow {
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(26, 35, 126, 0.05) 0%, transparent 70%);
  animation: rotate 20s linear infinite;
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Clock Section */
.clock-section {
  position: relative;
  z-index: 1;
}

.status-chip {
  font-weight: 700;
  letter-spacing: 0.5px;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.8; }
}

.date-display {
  font-size: 1.5rem;
  font-weight: 500;
  color: #616161;
  display: flex;
  align-items: center;
  justify-content: center;
}

.time-display {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 0.5rem;
}

.time-value {
  font-size: 4.5rem;
  font-weight: 700;
  color: #1a237e;
  line-height: 1;
  text-shadow: 0 4px 12px rgba(26, 35, 126, 0.2);
  font-variant-numeric: tabular-nums;
}

.time-period {
  font-size: 2rem;
  font-weight: 600;
  color: #1a237e;
  opacity: 0.7;
}

/* Instructions Section */
.instructions-section {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.05) 0%, rgba(26, 35, 126, 0.02) 100%);
  border-radius: 12px;
  padding: 16px;
}

/* Plate Input */
.plate-input :deep(input) {
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  color: #1a237e;
  letter-spacing: 2px;
  text-transform: uppercase;
}

.plate-input :deep(.v-field) {
  border: 2px solid #1a237e;
  border-radius: 12px;

}

.plate-input :deep(.v-field--focused) {
  border-color: #1a237e;
  box-shadow: 0 0 0 4px rgba(26, 35, 126, 0.1);
}

/* Action Buttons */
.action-buttons {
  margin-top: 2rem;
}

.action-btn {
  transition: all 0.3s ease;
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%) !important;
}

.action-btn:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(26, 35, 126, 0.4) !important;
}

.scan-btn {
  border: 2px solid #1a237e;
  transition: all 0.3s ease;
}

/* Divider Section */
.divider-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.divider-text {
  font-weight: 600;
  color: #9e9e9e;
  font-size: 0.875rem;
  padding: 0 0.5rem;
}

/* Activity Footer */
.activity-footer {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.05) 0%, rgba(26, 35, 126, 0.02) 100%);
  border-top: 1px solid rgba(26, 35, 126, 0.1);
}

/* Tips Card */
.tips-card {
  background: white;
  border-left: 4px solid #1a237e;
  animation: fadeInUp 0.8s ease-out 0.2s both;
}

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

/* Responsive Design */
@media (max-width: 960px) {
  .time-value {
    font-size: 3rem;
  }

  .time-period {
    font-size: 1.5rem;
  }

  .date-display {
    font-size: 1.25rem;
  }

  .plate-input :deep(input) {
    font-size: 1.5rem;
  }

  .action-btn,
  .scan-btn {
    height: 56px !important;
  }

  .action-btn .text-h6,
  .scan-btn .text-h6 {
    font-size: 1rem !important;
  }
}

@media (max-width: 600px) {
  .time-value {
    font-size: 2.5rem;
  }

  .time-period {
    font-size: 1.25rem;
  }

  .activity-footer .d-flex {
    flex-direction: column;
    gap: 1rem;
  }

  .activity-footer .v-divider {
    display: none;
  }
}

/* Loading State */
:deep(.v-btn--loading) {
  pointer-events: none;
}

/* Error State */
.plate-input :deep(.v-field--error) {
  border-color: #f44336 !important;
}
</style>
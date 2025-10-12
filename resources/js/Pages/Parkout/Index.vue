<template>
  <!-- QR Scanner Dialog -->
  <v-dialog v-model="isScanQR" max-width="600">
    <v-card rounded="lg">
      <v-card-title class="pa-6 pb-4">
        <div class="d-flex align-center">
          <v-icon size="28" color="indigo-darken-4" class="mr-3">mdi-qrcode-scan</v-icon>
          <span class="text-h6 font-weight-bold">Scan QR Code</span>
        </div>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text class="pa-6">
        <div id="reader" style="width:100%; height:400px; border-radius: 12px; overflow: hidden;"></div>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions class="pa-4">
        <v-spacer></v-spacer>
        <v-btn color="error" variant="outlined" @click="closeScanner">
          <v-icon start>mdi-close</v-icon>
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
  

      <v-dialog v-model="showConfirmDialog" max-width="500" persistent>
        <v-card rounded="lg" class="confirm-card">
          <!-- Header -->
          <div class="form-header pa-6 pb-4">
            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <v-avatar color="indigo-darken-4" size="48" class="mr-3">
                  <v-icon size="28" color="white">mdi-check-decagram</v-icon>
                </v-avatar>
                <div>
                  <h2 class="text-h6 font-weight-bold text-indigo-darken-4">
                    Confirm Sale
                  </h2>
                  <p class="text-caption text-medium-emphasis mb-0">
                    Mark card as sold in the system
                  </p>
                </div>
              </div>
              <v-btn
                icon="mdi-close"
                variant="text"
                @click="cancelConfirm"
              ></v-btn>
            </div>
          </div>

          <v-divider></v-divider>

          <!-- Content -->
          <v-card-text class="pa-6">
            <v-alert
              type="info"
              variant="tonal"
              density="comfortable"
              class="sale-alert mb-0"
            >
              <div class="text-body-2">
                You are about to mark card 
                <span class="font-weight-bold text-indigo-darken-4">{{ selectedCardToConfirm?.card_number }}</span> 
                as <span class="font-weight-bold">CONFIRMED</span>.
              </div>
              <div class="text-caption text-medium-emphasis mt-2">
                <v-icon size="16" class="mr-1">mdi-information-outline</v-icon>
                This action will update the card's status permanently.
              </div>
            </v-alert>
          </v-card-text>

          <v-divider></v-divider>

          <!-- Actions -->
          <v-card-actions class="pa-6">
            <v-spacer />
            <v-btn
              variant="outlined"
              size="large"
              @click="cancelConfirm"
              prepend-icon="mdi-close"
            >
              Cancel
            </v-btn>
            <v-btn
              color="indigo-darken-4"
              variant="flat"
              size="large"
              @click="confirmStatusChange"
              prepend-icon="mdi-check-circle"
            >
              Confirm Sale
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>


  <!-- Main Layout -->
  <div class="parkout-wrapper">
    <v-container fluid class="fill-height">
      <v-row justify="center" align="center" class="fill-height">
        <v-col cols="12" md="8" lg="6">
          <!-- Main Entry Card -->

            
            <!-- Status Section -->
            <div class="status-section pa-8 text-center">
              <v-chip 
                color="error" 
                variant="flat" 
                size="large"
                class="mb-4 status-chip"
              >
                <v-icon start>mdi-car-arrow-left</v-icon>
                EXIT PROCESSING
              </v-chip>

              <div class="icon-wrapper mb-6">
                <v-avatar color="error" size="100" class="exit-avatar">
                  <v-icon size="60" color="white">mdi-car-arrow-left</v-icon>
                </v-avatar>
              </div>

              <!-- Instructions Section -->
              <div class="instructions-section mb-6">
                <h3 class="text-h6 font-weight-bold text-indigo-darken-4 mb-2">
                  <v-icon class="mr-2">mdi-information-outline</v-icon>
                  Vehicle Exit
                </h3>
                <p class="text-body-2 text-medium-emphasis">
                  Enter the vehicle plate number or scan the parking ticket to process exit
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
                  @click="submitPlate"
                  block
                  color="indigo-darken-4"
                  :disabled="form.processing || !form.plate_no"
                  :loading="form.processing"
                  class="mb-4 action-btn"
                  height="64"
                >
                  <v-icon start size="28">mdi-exit-run</v-icon>
                  <span class="text-h6 font-weight-bold">Process Exit</span>
                </v-btn>

                <div class="divider-section mb-4">
                  <v-divider></v-divider>
                  <span class="divider-text">OR</span>
                  <v-divider></v-divider>
                </div>

                <v-btn
                  size="x-large"
                  block
                  color="indigo-darken-4"
                  variant="outlined"
                  class="scan-btn"
                  height="64"
                  @click="scanQR"
                >
                  <v-icon start size="28">mdi-qrcode-scan</v-icon>
                  <span class="text-h6 font-weight-bold">Scan Ticket QR</span>
                </v-btn>
              </div>
            </div>

            <!-- Stats Footer -->
            <div class="stats-footer pa-4">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-avatar color="info" size="32" class="mr-3">
                    <v-icon size="20">mdi-clock-outline</v-icon>
                  </v-avatar>
                  <div>
                    <p class="text-caption text-medium-emphasis mb-0">Current Time</p>
                    <p class="text-subtitle-2 font-weight-bold mb-0">
                      {{ now.format('h:mm A') }}
                    </p>
                  </div>
                </div>

                <v-divider vertical class="mx-4"></v-divider>

                <div class="d-flex align-center">
                  <v-avatar color="warning" size="32" class="mr-3">
                    <v-icon size="20">mdi-calendar-today</v-icon>
                  </v-avatar>
                  <div>
                    <p class="text-caption text-medium-emphasis mb-0">Today</p>
                    <p class="text-subtitle-2 font-weight-bold mb-0">
                      {{ now.format('MMM D, YYYY') }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
         

          <!-- Quick Tips Card -->
          <v-card class="tips-card elevation-8 mt-6" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center mb-3">
                <v-icon color="indigo-darken-4" class="mr-2">mdi-help-circle</v-icon>
                <h4 class="text-subtitle-1 font-weight-bold">Exit Process Tips</h4>
              </div>
              <v-list density="compact" class="bg-transparent">
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon size="20" color="success">mdi-check-circle</v-icon>
                  </template>
                  <v-list-item-title class="text-body-2">
                    Enter plate number without spaces or dashes
                  </v-list-item-title>
                </v-list-item>
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon size="20" color="success">mdi-check-circle</v-icon>
                  </template>
                  <v-list-item-title class="text-body-2">
                    Scan QR code from parking ticket for faster processing
                  </v-list-item-title>
                </v-list-item>
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon size="20" color="success">mdi-check-circle</v-icon>
                  </template>
                  <v-list-item-title class="text-body-2">
                    Payment amount will be calculated automatically
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3"
import { computed, ref } from "vue"
import { route } from "ziggy-js"
import { Html5Qrcode } from 'html5-qrcode'
import dayjs from "dayjs"

const page = usePage()
const ticket = computed(() => page.props.ticket || null)
const success = computed(() => page.props.success || false)

const ticketId = ref(null)
const messages = ref([])

const isScanQR = ref(false)
const html5QrCode = ref(null)

const now = dayjs()
const formatDate = (date) => dayjs(date).format("MM/DD/YYYY")
const formatFee = (fee) => `${Number(fee).toFixed(2)} PHP`

const form = useForm({
  plate_no: "",
  park_out_year: now.year(),
  park_out_month: now.month() + 1,
  park_out_day: now.date(),
  park_out_hour: now.hour(),
  park_out_minute: now.minute(),
  park_out_second: now.second(),
})

const clearError = () => {
  // Clear errors if needed
}

const cancelPayment = () => {
  router.visit(route("parkout"))
}

const submitPlate = () => {
  clearError()
  form.post(route('parkout.submit'), {
    onSuccess: () => form.reset(),
    onError: (errors) => {
      if (errors.plate_no) {
        // Handle errors
      }
    },
  })
}

const scanQR = async () => {
  isScanQR.value = true
  setTimeout(startScanner, 300)
}

const startScanner = async () => {
  clearError()
  if (!isScanQR.value) return

  try {
    html5QrCode.value = new Html5Qrcode('reader')
    await html5QrCode.value.start(
      { facingMode: 'environment' },
      { fps: 10, qrbox: { width: 250, height: 250 } },
      async (decodedText) => {
        console.log('QR code detected ✅', decodedText)
        await closeScanner()
        router.post(route('parkout.submit'), {
          qr_code: decodedText,
          is_scan_qr: true
        })
      }
    )
  } catch (err) {
    console.error('Error starting camera:', err.message)
  }
}

const closeScanner = async () => {
  if (html5QrCode.value) {
    try {
      await html5QrCode.value.stop()
      await html5QrCode.value.clear()
    } catch (e) {
      console.error('Error closing scanner:', e)
    }
    html5QrCode.value = null
  }
  isScanQR.value = false
}
</script>

<style scoped>
.parkout-wrapper {

  min-height: 100vh;
  position: relative;
  overflow: hidden;
}

.parkout-wrapper::before {
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
  border: 1px solid rgba(183, 28, 28, 0.1);
  animation: fadeInScale 0.6s ease-out;
}

.card-glow {
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(183, 28, 28, 0.05) 0%, transparent 70%);
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

/* Status Section */
.status-section {
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

.exit-avatar {
  box-shadow: 0 8px 24px rgba(183, 28, 28, 0.3);
  animation: exitFloat 3s ease-in-out infinite;
}

@keyframes exitFloat {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
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
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.02) 0%, white 100%);
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

.scan-btn:hover {
  background: rgba(26, 35, 126, 0.04);
  transform: translateY(-2px);
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

/* Stats Footer */
.stats-footer {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.05) 0%, rgba(26, 35, 126, 0.02) 100%);
  border-top: 1px solid rgba(26, 35, 126, 0.1);
}

/* Tips Card */
.tips-card {
  background: white;
  border-left: 4px solid #b71c1c;
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
  .stats-footer .d-flex {
    flex-direction: column;
    gap: 1rem;
  }

  .stats-footer .v-divider {
    display: none;
  }
}
</style>
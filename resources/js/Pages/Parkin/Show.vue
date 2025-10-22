<template>
  <div class="ticket-wrapper">
    <v-container fluid class="py-8">






      <v-row justify="center" align="center" class="fill-height">
        <v-col cols="12" md="6" lg="5">
          <!-- Success Header -->
          <div class="success-header mb-6 text-center">
            <v-avatar color="success" size="80" class="mb-4 success-avatar">
              <v-icon size="50" color="white">mdi-check-circle</v-icon>
            </v-avatar>
            <h1 class="text-h4 font-weight-bold mb-2 text-success">
            Entry Successful!
            </h1>

            <p class="text-indigo-darken-4 text-h6 ">
            Your parking ticket has been generated
            </p>
                    </div>

          <!-- Ticket Card -->
               <div class="actions-section pa-6">
              <v-row dense>
                <v-col cols="12" sm="6">
                  <v-btn
                    color="indigo-darken-4"
                    size="large"
                    block
                    variant="flat"
                    prepend-icon="mdi-printer"
                    @click="printTicket"
                  >
                    Print Ticket
                  </v-btn>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-btn
                    color="white"
                    size="large"
                    block
                    variant="flat"
                    prepend-icon="mdi-home"
                    @click="goToDashboard"
                  >
                    Back to Dashboard
                  </v-btn>
                </v-col>
              </v-row>
            </div>
          <!-- Footer Note -->
          <div class="footer-note text-center mt-6">
            <p class="text-body-2 text-white-70">
              Thank you for choosing our parking facility
            </p>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import QrcodeVue from 'qrcode.vue'
import dayjs from 'dayjs'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  ticket: Object
})

const qrValue = props.ticket.qr_code

const formattedDate = dayjs(
  `${props.ticket.park_year}-${props.ticket.park_month}-${props.ticket.park_day}`
).format('MMMM D, YYYY')

const formattedTime = dayjs(
  `${props.ticket.park_year}-${props.ticket.park_month}-${props.ticket.park_day} ${props.ticket.park_hour}:${props.ticket.park_minute}:${props.ticket.park_second}`
).format('h:mm A')

const printTicket = () => {
   window.open(route('print.ticket', { uuid: props.ticket.uuid }), '_blank')
}

const goToDashboard = () => {
  router.visit(route('dashboard'))
}
</script>

<style scoped>
.ticket-wrapper {

  min-height: 100vh;
  padding: 2rem 0;
}

.text-white-70 {
  color: rgba(255, 255, 255, 0.9);
}
    
/* Success Header */
.success-header {
  animation: fadeInDown 0.6s ease-out;
}

.success-avatar {
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.4);
  animation: successPulse 2s ease-in-out infinite;
}

@keyframes successPulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 8px 24px rgba(76, 175, 80, 0.4);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 12px 32px rgba(76, 175, 80, 0.6);
  }
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Ticket Card */
.ticket-card {
  background: white;
  animation: fadeInUp 0.8s ease-out;
  max-width: 600px;
  margin: 0 auto;
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

.ticket-header {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
}

/* QR Code Section */
.qr-section {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.02) 0%, transparent 100%);
}

.qr-wrapper {
  display: inline-block;
  padding: 16px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  border: 4px solid #1a237e;
}

.qr-code {
  display: block;
  border-radius: 8px;
}

/* Info Cards */
.info-card {
  border: 1px solid rgba(0, 0, 0, 0.08);
  transition: all 0.2s ease;
}

.info-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Instructions */
.instruction-card {
  border: 2px solid #c5cae9;
}

/* Actions */
.actions-section :deep(.v-btn) {
  transition: all 0.3s ease;
}

.actions-section :deep(.v-btn:hover) {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

/* Print Styles */
@media print {
  .ticket-wrapper {
    background: white;
  }

  .success-header,
  .footer-note,
  .actions-section {
    display: none;
  }

  .ticket-card {
    box-shadow: none;
    max-width: 100%;
  }
}

/* Responsive */
@media (max-width: 600px) {
  .success-header h1 {
    font-size: 1.75rem;
  }

  .success-header p {
    font-size: 1rem;
  }

  .qr-wrapper {
    padding: 12px;
  }
}
</style>
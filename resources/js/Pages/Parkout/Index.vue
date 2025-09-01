<template>
  <v-snackbar-queue
    v-model="messages"
    location="top"
    :prepend-icon="messages.icon"
  />

  <!-- QR Scanner Dialog -->
  <v-dialog v-model="isScanQR" max-width="600">
    <v-card>
      <v-card-title>Scan QR Code</v-card-title>
      <v-card-text>
        <div id="reader" style="width:100%; height:400px;"></div>
      </v-card-text>
      <v-card-actions>
        <v-btn color="error" text @click="closeScanner">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Error Card -->
    <v-dialog v-model="showErrorCard" max-width="400" persistent>
        <v-card elevation="16">
            <v-card-title class="text-h6">Error</v-card-title>
            <v-card-text>{{ errorCardMsg }}</v-card-text>
            <v-card-actions class="justify-center">
                <v-btn variant="flat" color="primary" @click="showErrorCard = false"
                >Ok</v-btn
                >
            </v-card-actions>
        </v-card>
    </v-dialog>

  <!-- Main Layout -->
    <v-layout class="d-flex flex-column align-center justify-center pa-16 h-100">
    <!-- Show ticket if found -->
        <v-card v-if="success && ticket" class="pa-5">
        <v-card-text class="py-1 px-2">
            Park in Date Time: {{ formatDate(ticket.PARKDATETIME) }}
        </v-card-text>
        <v-card-text class="py-1 px-2">
            Park out Date Time: {{ formatDate(ticket.PARKOUTDATETIME) }}
        </v-card-text>
        <v-card-text class="py-1 px-2">
            Ticket Fee: {{ formatFee(ticket.PARKFEE) }}
        </v-card-text>

            <v-layout class="d-flex justify-space-between mt-2 ga-4">
                <v-btn @click="submitPayment" color="blue-darken-4">Cash</v-btn>
                <v-btn @click="scanQR" color="blue-darken-4">Scan QR</v-btn>
                <v-btn @click="cancelPayment" color="red-darken-2">Cancel</v-btn>
            </v-layout>
        </v-card>

            <!-- Show input if no ticket -->
        <div v-else>
                <v-text-field
                class="text-h1"
                v-model="form.PLATENO"
                placeholder="Enter Plate No."
                style="width: 300px"
                variant="underlined"
                @input="form.PLATENO = form.PLATENO.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()"
                />

        <v-btn
            :disabled="!form.PLATENO"
            size="x-large"
            @click="submitPlate"
            block
            color="blue-darken-4"
        >
            Submit
        </v-btn>
    </div>
  </v-layout>
</template>

<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3"
import { computed, ref, watch, onBeforeUnmount } from "vue"
import { route } from "ziggy-js"
import dayjs from "dayjs"
import { Html5Qrcode } from "html5-qrcode"

const page = usePage()
const ticket = computed(() => page.props.ticket || null)
const success = computed(() => page.props.success || false)

const errorCardMsg = ref("")
const showErrorCard = ref(false)
const ticketId = ref(null)
const messages = ref([])
const isScanQR = ref(false)

let html5QrCode = null

watch(ticket, (newTicket) => {
  if (newTicket) ticketId.value = newTicket.id
})

// 👀 watch for flash error from Laravel
watch(
  () => page.props.flash?.error,
  (val) => {
    if (val) {
      showErrorCard.value = true
      errorCardMsg.value = val
    }
  }
)

const now = dayjs()
const formatDate = (date) => dayjs(date).format("MM/DD/YYYY")
const formatFee = (fee) => `${Number(fee).toFixed(2)} PHP`

const form = useForm({
  PLATENO: "",
  PARKOUTYEAR: now.year(),
  PARKOUTMONTH: now.month() + 1,
  PARKOUTDAY: now.date(),
  PARKOUTHOUR: now.hour(),
  PARKOUTMINUTE: now.minute(),
  PARKOUTSECOND: now.second(),
})

const form2 = useForm({
  ID: ticketId.value || null,
})

const clearError = () => {
  errorCardMsg.value = ""
  showErrorCard.value = false
}

const cancelPayment = () => {
  router.visit(route("parkout"))
}

const submitPlate = () => {
  clearError()
  form.post(route('parkout'), {
    onSuccess: () => form.reset(),
    onError: (errors) => {
      if (errors.PLATENO) {
        showErrorCard.value = true
        errorCardMsg.value = errors.PLATENO
      }
    },
  })
}

const submitPayment = () => {
  form2.ID = ticketId.value
  clearError()
  form2.post("/submit/payment", {
    onSuccess: () => {
      clearError()
      messages.value.push({
        icon: "mdi-check-circle",
        text: "Payment successful!",
        timeout: 3000,
      })
    },
    onError: () => {
      showErrorCard.value = true
      errorCardMsg.value = "Payment failed. Please try again."
    },
  })
}

const scanQR = async () => {
  isScanQR.value = true
  setTimeout(() => startScanner(), 300)
}

const startScanner = async () => {
    if (!isScanQR.value) return
    try {
        html5QrCode = new Html5Qrcode("reader")
        await html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        async (decodedText) => {
            console.log("QR code detected ✅", decodedText)
            await closeScanner()
            router.post(route("process.qr"), { qr_code: decodedText,   ticket_id: ticketId.value })
        }
        )
    } catch (err) {
        showErrorCard.value = true
        errorCardMsg.value = "Error starting camera: " + err.message
        }
}

const closeScanner = async () => {
  if (html5QrCode) {
    try {
      await html5QrCode.stop()
      await html5QrCode.clear()
    } catch (e) {}
    html5QrCode = null
  }
  isScanQR.value = false
}

onBeforeUnmount(() => {
  closeScanner()
})
</script>

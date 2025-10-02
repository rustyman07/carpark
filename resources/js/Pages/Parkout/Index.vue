<template>
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

  <!-- Main Layout -->
    <v-layout class="d-flex flex-column align-center justify-center pa-5 h-100">
    <!-- Show ticket if found -->


            <!-- Show input if no ticket -->
         <v-container   max-width ="450">
             <v-text-field
            v-model="form.plate_no"
            placeholder="Enter Plate No." 
            variant="underlined"
            :error-messages="form.errors.plate_no"
            @input="form.plate_no = form.plate_no.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()"
            class="text-center-input "
            />

        <v-btn
          :disabled="form.processing ||!form.plate_no"
            size="x-large"
            @click="submitPlate"
            block
            color="blue-darken-4"
        >
            Submit
        </v-btn>
                  <v-layout class="d-flex ga-2 align-center">
                    <v-divider thickness="2"></v-divider>   <span>OR</span><v-divider thickness="2"></v-divider>
                </v-layout>     
                <v-btn size="x-large" @click="scanQR" block color="blue-darken-4">
                    Scan 
                </v-btn>
       </v-container>
  </v-layout>
</template>

<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3"
import { computed, ref, watch, onBeforeUnmount } from "vue"
import { route } from "ziggy-js"
import { Html5Qrcode } from 'html5-qrcode';
import dayjs from "dayjs"


const page = usePage()
const ticket = computed(() => page.props.ticket || null)
const success = computed(() => page.props.success || false)

// const errorCardMsg = ref("")
// const showErrorCard = ref(false)
const ticketId = ref(null)
const messages = ref([])

const isScanQR = ref(false);
const html5QrCode = ref(null);


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
  // errorCardMsg.value = ""
  // showErrorCard.value = false
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

        
 
      }
    },
  })
}


const scanQR = async () => {
    isScanQR.value = true;
    // Delay scanner start to allow the modal/component to render
    setTimeout(startScanner, 300);
};

const startScanner = async () => {
      clearError()
    if (!isScanQR.value) return;

    try {
        html5QrCode.value = new Html5Qrcode('reader');
        await html5QrCode.value.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            async (decodedText) => {
                console.log('QR code detected ✅', decodedText);
                await closeScanner();
                router.post(route('parkout.submit'), {
                    qr_code: decodedText,
                    is_scan_qr: true
                  
                });
            }
        );
    } catch (err) {
        console.error('Error starting camera:', err.message);
    }
};

const closeScanner = async () => {
    if (html5QrCode.value) {
        try {
            await html5QrCode.value.stop();
            await html5QrCode.value.clear();
        } catch (e) {
            console.error('Error closing scanner:', e);
        }
        html5QrCode.value = null;
    }
    isScanQR.value = false;
};

</script>


<style scoped>

.text-center-input :deep input {
  text-align: center;
    font-size: 2.5rem;
  font-weight: bold;
    color: #616161;
}
/* .time {
  margin-top: -20px;
  font-size: 6em;
  font-weight: bold;
  color: #616161;

} */
</style>
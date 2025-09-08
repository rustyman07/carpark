<template>
  <!-- QR Scanner Dialog -->
  <!-- Error Card -->
    <!-- <v-dialog v-model="showErrorCard" max-width="400" persistent>
        <v-card elevation="16">
            <v-card-title class="text-h6">Error</v-card-title>
            <v-card-text>{{ errorCardMsg }}</v-card-text>
            <v-card-actions class="justify-center">
                <v-btn variant="flat" color="primary" @click="showErrorCard = false"
                >Ok</v-btn
                >
            </v-card-actions>
        </v-card>
    </v-dialog> -->

  <!-- Main Layout -->
    <v-layout class="d-flex flex-column align-center justify-center pa-16 h-100">
    <!-- Show ticket if found -->


            <!-- Show input if no ticket -->
         <v-container   max-width ="450">
             <v-text-field
            v-model="form.PLATENO"
            placeholder="Enter Plate No." 
            variant="underlined"
            :error-messages="form.errors.PLATENO"
            @input="form.PLATENO = form.PLATENO.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()"
            class="text-center-input "
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
       </v-container>
  </v-layout>
</template>

<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3"
import { computed, ref, watch, onBeforeUnmount } from "vue"
import { route } from "ziggy-js"
import dayjs from "dayjs"


const page = usePage()
const ticket = computed(() => page.props.ticket || null)
const success = computed(() => page.props.success || false)

// const errorCardMsg = ref("")
// const showErrorCard = ref(false)
const ticketId = ref(null)
const messages = ref([])




// watch(ticket, (newTicket) => {
//   if (newTicket) ticketId.value = newTicket.id
// })

// 👀 watch for flash error from Laravel
// watch(
//   () => page.props.flash?.error,
//   (val) => {
//     if (val) {
//       showErrorCard.value = true
//       errorCardMsg.value = val
//     }
//   }
// )

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
      if (errors.PLATENO) {

        
 
      }
    },
  })
}


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
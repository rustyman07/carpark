<template>
    <v-container class="d-flex justify-center mt-6">
        <v-card ref="receiptContent" elevation="1" width="300" class="pa-4">
        <!-- Header -->
        <div class="text-center mb-2">
            <p class="text-grey">Thank you for parking with us</p>
            <v-divider class="my-2"></v-divider>
            <h3 class="font-weight-bold">Parking Receipt</h3>
            <v-divider class="my-2"></v-divider>
        </div>
        <div  class="d-flex flex-column items-center align-center">
            <p class=" text-caption">Ticket No.</p>
            <p class="font-weight-bold ">{{ticket.TICKETNO }}</p>
        </div>

            <div class="d-flex  text-body-2 pb-2">
                <p class="font-weight-bold w-25">Plate No</p>
                <p class="w-10"> : </p>
                <p class="ml-2">{{ticket.PLATENO }}</p>
            </div>

            <div class="d-flex text-body-2 pb-2">
                <p class="font-weight-bold w-25" >From </p>
                <p class="w-10"> : </p>
                <p class="ml-2" >{{ parkinDate }}</p>
                <p  class="ml-4" >{{ parkinTime }}</p>
            </div>          
            <div class="d-flex ">
                <p  class="font-weight-bold w-25 pb-2">To</p>
                <p class="w-10 "> : </p>
                <p class="ml-2">{{ parkoutDate }}</p>
                <p  class="ml-4" >{{ parkoutTime }}</p>
            </div>
                <!-- <div class="d-flex flex-column w-25">
                    <p>{{ parkinTime }}</p>
                    <p>{{ parkoutTime }}</p>
                </div> -->
                
           
            <div class="d-flex  text-body-2 pb-2 ">
                <p  class="font-weight-bold w-25" >Duration</p>
                <p class="w-10 "> : </p>
                <p class="ml-2">{{ duration }}</p>
            </div>
            <div class="d-flex  text-body-2 ">
                <p  class="font-weight-bold w-25">Balance </p>
                <p class="w-10 "> : </p>
                <p class="ml-2">{{detail.balance }}</p>
            </div>
        <!-- Receipt Body -->
        <!-- <v-row class="mb-2">
            <v-col cols="6" class="font-weight-bold">From: <span>{{parkinDate}}</span> </v-col>
            <v-col cols="6">{{ plate }}</v-col>
        </v-row>
        <v-row class="mb-2">
            <v-col cols="6" class="font-weight-bold">To:</v-col>
            <v-col cols="6">{{ new Date().toLocaleString() }}</v-col>
        </v-row>
        <v-row class="mb-2">
            <v-col cols="6" class="font-weight-bold">Duration:</v-col>
            <v-col cols="6">{{ duration }} hours</v-col>
        </v-row>
        <v-row class="mb-2">
            <v-col cols="6" class="font-weight-bold">Amount:</v-col>
            <v-col cols="6">₱{{ amount }}</v-col>
        </v-row> -->

        <v-divider class="my-4"></v-divider>

        <!-- Footer -->
        <div class="text-center">
            <!-- <p class="text-grey text-caption">Powered by Your Company</p> -->
            <v-btn data-html2pdf-ignore color="primary" block @click="downloadPdf">Download PDF</v-btn>
        </div>
        </v-card>
  </v-container>
</template>

<script setup>
import { ref,computed } from "vue"
import html2pdf from "html2pdf.js"
import { usePage } from "@inertiajs/vue3"
import dayjs from 'dayjs'
import durationPlugin from 'dayjs/plugin/duration'

const page = usePage();
const ticket = page.props.ticket
const detail = page.props.detail



console.log("PARKDATETIME:", ticket?.PARKDATETIME)
console.log("PARKOUTDATETIME:", ticket?.PARKOUTDATETIME)
// Format entry time
const parkinDate = computed(() => {

  return ticket?.PARKDATETIME 
    ? dayjs(ticket.PARKDATETIME).format("MM/DD/YYYY")
    : null
})


const parkinTime = computed(() => {
  return ticket?.PARKDATETIME
    ? dayjs(ticket.PARKDATETIME).format("hh:mm A") // ex: 03:45 PM
    : null
})





// Format exit time
const parkoutDate = computed(() => {
  return ticket?.PARKOUTDATETIME 
    ? dayjs(ticket.PARKOUTDATETIME).format("MM/DD/YYYY")
    : null
})

const parkoutTime = computed(() => {
  return ticket?.PARKOUTDATETIME
    ? dayjs(ticket.PARKOUTDATETIME).format("hh:mm A") // ex: 03:45 PM
    : null
})



const duration = computed(() => {
  if (!ticket?.PARKDATETIME || !ticket?.PARKOUTDATETIME) return null

  const start = dayjs(ticket.PARKDATETIME)
  const end = dayjs(ticket.PARKOUTDATETIME)

  // Difference in minutes
  const diffMinutes = end.diff(start, 'minute')

  const days = Math.floor(diffMinutes / (60 * 24))
  const hours = Math.floor((diffMinutes % (60 * 24)) / 60)
  const minutes = diffMinutes % 60

  // Format smartly
  if (days > 0) {
    return `${days}d ${hours}h ${minutes}m`
  } else if (hours > 0) {
    return `${hours}h ${minutes}m`
  } else {
    return `${minutes}m`
  }
})


const receiptContent = ref(null)

const downloadPdf = () => {
  const element = receiptContent.value?.$el ?? receiptContent.value
  html2pdf()
    .set({
      margin: 10,
      filename: `receipt-${ticket.PLATENO}.pdf`,
      image: { type: "jpeg", quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
    })
    .from(element)
    .save()
}
</script>

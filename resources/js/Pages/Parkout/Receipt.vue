<template>
    <v-container class="d-flex justify-center align-center fill-height" >
        <v-card ref="receiptContent" elevation="1" min-width="300" class="pa-4">
        <!-- Header -->
		 <div class="d-flex flex-column   align-center justify-center ">
			<p  >{{ company.name }}</p>
			<p>{{ company.address }}</p>
			<p>{{ company.contact }}</p>
		 </div>

        <div class="text-center mb-2">
            <p class="text-grey">Thank you for parking with us</p>
        </div>
            <!-- <v-icon icon="mdi-parking"></v-icon> -->
            <v-divider class="my-2"></v-divider>
            <h3 class="font-weight-bold">Parking Receipt</h3>
            <v-divider class="my-2"></v-divider>

        <div  class="d-flex flex-column align-center ">
            <p class=" text-caption">Ticket No.</p>
            <h2 class="font-weight-bold ">{{props.ticket.ticket_no }}</h2>
        </div>

            <div class="d-flex  text-body-2 pb-2 mt-4">
                <p class="font-weight-bold w-25">Plate No</p>
                <p class="w-10"> : </p>
                <p class="ml-2">{{props.ticket.plate_no }}</p>
            </div>

            <div class="d-flex text-body-2 pb-2">
                <p class="font-weight-bold w-25" >From </p>
                <p class="w-10"> : </p>
                <p class="ml-2" >{{ parkinDate }}</p>
                <p  class="ml-4" >{{ parkinTime }}</p>
            </div>          
            <div class="d-flex  text-body-2 pb-2">
                <p  class="font-weight-bold w-25 ">To</p>
                <p class="w-10 "> : </p>
                <p class="ml-2">{{ parkoutDate }}</p>
                <p  class="ml-4" >{{ parkoutTime }}</p>
            </div>
       
            <div class="d-flex  text-body-2 pb-2 ">
                <p  class="font-weight-bold w-25" >Duration</p>
                <p class="w-10 "> : </p>
                <p class="ml-2">{{ duration }}</p>
            </div>

           <div v-if="ticket.mode_of_payment === 'card'" class="d-flex text-body-2">
				<p class="font-weight-bold w-25">Balance </p>
				<p class="w-10 "> : </p>
				<p class="ml-2">{{ props.balance }}</p>
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


const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
  balance: {
    type: Number,
    default: null,
  },
  company:  Object,

})

const page = usePage();



// const ticket = page.props.ticket





// Format entry time
const parkinDate = computed(() => {

  return props.ticket?.park_datetime 
    ? dayjs(props.ticket.park_datetime).format("MM/DD/YYYY")
    : null
})


const parkinTime = computed(() => {
  return props.ticket?.park_datetime
    ? dayjs(props.ticket.park_datetime).format("hh:mm A") 
    : null
})



// Format exit time
const parkoutDate = computed(() => {
  return props.ticket?.parkout_datetime 
    ? dayjs(props.ticket.parkout_datetime).format("MM/DD/YYYY")
    : null
})

const parkoutTime = computed(() => {
  return props.ticket?.parkout_datetime
    ? dayjs(props.ticket.parkout_datetime).format("hh:mm A") 
    : null
})



const duration = computed(() => {
  if (!props.ticket?.park_datetime || !props.ticket?.parkout_datetime) return null

const start = dayjs(props.ticket.park_datetime).startOf('minute')
const end = dayjs(props.ticket.parkout_datetime).startOf('minute')


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

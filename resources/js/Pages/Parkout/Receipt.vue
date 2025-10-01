<template>
    <v-container class="d-flex justify-center align-center fill-height" >
        <v-card ref="receiptContent" elevation="1" min-width="350" class="py-4">
        <!-- Header -->
		 <div class="flex flex-column   align-center justify-center text-caption ">
			<span  >{{ company.name }}</span>
			<p>{{ company.address }}</p>
			<p>{{ company.contact }}</p>
		 </div>

        <!-- <div class="text-center mb-2 text-caption">
            <span class="">Thank you for parking with us</span>
        </div> -->
            <!-- <v-icon icon="mdi-parking"></v-icon> -->
 
        <div class="text-sm text-gray-500 px-4">
            
            <div  class="flex justify-between  mt-4 py-1  ">
                <span class=" ">Ticket No.</span>
                <span class="font-medium ml-2  text-sm ">{{props.ticket.ticket_no }}</span>
            </div>

            <div class="flex justify-between   py-1 "  >
                <span class="w-25">Plate No</span>
                <span class=" font-medium ml-2  text-sm">{{props.ticket.plate_no }}</span>
            </div>
                 
            <div class="d-flex justify-between  py-1 ">
                <span class="w-25" >From </span>
                <div class="flex text-sm">
                <span class="ml-2 font-medium " >{{ parkinDate }}</span>
                <span  class="ml-4 font-medium " >{{ parkinTime }}</span>
                </div>        
            </div>     
             
            <div class="flex justify-between py-1 ">
                <span  class=" w-25 ">To</span>
                <div class="flex  text-sm">
                    <span class="ml-2 font-medium ">{{ parkoutDate }}</span>
                    <span  class="ml-4 font-medium " >{{ parkoutTime }}</span>
                </div>
            </div>

            <div class="flex  justify-between py-1">
                <span  class=" w-25" >Duration</span>
                <span class="ml-2  font-medium  text-sm ">{{ duration }}</span>
            </div>

            
            <!-- <v-divider></v-divider> -->
           
                   <div class="flex justify-between py-1 ">
                <span class=" w-25">Park Fee</span>
                <span class="ml-2 font-medium  text-sm ">{{formatCurrency(props.ticket.park_fee) }}</span>
            </div>

    

           <!-- <div v-if="ticket.mode_of_payment === 'card'" class="d-flex ">
				<span class=" w-25">Balance </span>
				<span class="w-10 "> : </span>
				<span class="ml-2">{{ props.balance }}</span>


			</div> -->

                 
        <v-data-table-server v-if = "filteredCard.length"
       class="border border-gray-300 rounded"
         density="compact"
          :headers="headers"
          :items="filteredCard"
          :items-per-page="5"
          :loading="false"
             flat
          disable-sort
          hide-default-footer
        >
       
        </v-data-table-server>
                <div class="">
                <ul>
                    <div v-for = "item in filteredCash" class="flex justify-between py-1 ">
                        <div> Paid in Cash</div>
                        <div class="font-medium"> {{  item.amount}}</div>
                    </div>
                </ul>
            </div>

            <div class="flex justify-between py-1  ">
                <span  class=" w-25" >Amount</span>
                <span class="ml-2 font-medium  text-sm">{{formatCurrency(props.payment.amount) }}</span>
            </div>
              <div class="flex justify-between py-1">
                <span class="font-semibold" >Total Amount</span>
                <span class="ml-2 font-medium  text-sm">{{formatCurrency(props.payment.total_amount) }}</span>
            </div>
          <v-divider></v-divider>
            <div class="flex justify-between py-1">
                <span  >Change</span>
             
                <span class="ml-2 font-medium  text-sm">{{formatCurrency(props.payment.change) }}</span>
            </div>
        </div>
              

						<!-- Receipt Body -->
        <!-- <v-row class="mb-2">
            <v-col cols="6" class="">From: <span>{{parkinDate}}</span> </v-col>
            <v-col cols="6">{{ plate }}</v-col>
        </v-row>
        <v-row class="mb-2">
            <v-col cols="6" class="">To:</v-col>
            <v-col cols="6">{{ new Date().toLocaleString() }}</v-col>
        </v-row>
        <v-row class="mb-2">
            <v-col cols="6" class="">Duration:</v-col>
            <v-col cols="6">{{ duration }} hours</v-col>
        </v-row>
        <v-row class="mb-2">
            <v-col cols="6" class="">Amount:</v-col>
            <v-col cols="6">₱{{ amount }}</v-col>
        </v-row> -->

     

        <!-- Footer -->
        <!-- <div class="text-center">
    
            <v-btn data-html2pdf-ignore color="primary" block @click="downloadPdf">Print</v-btn>
        </div> -->
        </v-card>
  </v-container>
</template>

<script setup>
import { ref,computed } from "vue"
import html2pdf from "html2pdf.js"
import { usePage } from "@inertiajs/vue3"
import dayjs from 'dayjs'
import { formatCurrency } from '@/utils/utility';


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
  amount: Number,
  details: Array,
  payment: Object,

})

const page = usePage();



// const ticket = page.props.ticket

const headers = [
  { key: 'card_number', title: 'Card Number' },
//   { key: 'no_of_days', title: 'No. of Days' },
//   { key: 'price', title: 'Price' },
    { key: 'amount', title: 'Amount Deducted' },
  { key: 'balance', title: 'Balance', class: 'bg-blue-darken-4' },
]


const filteredCard = computed(()=>{
   return props.details.filter((a)=>a.card_id !== null);
})


const filteredCash = computed(()=>{
   return props.details.filter((a)=>a.card_id == null);
})


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

<template>
<v-snackbar-queue v-model="messages" location="top"  :prepend-icon="messages.icon"></v-snackbar-queue>
<v-card
  v-if="showErrorCard"
  class="error-card pa-6 text-center position-absolute"
  style="top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2000;"
   elevation="16"
>
  <v-card-title class="text-h6">Error</v-card-title>
  <v-card-text>
    {{  errorCardMsg }}
  </v-card-text>
  <v-card-actions class="justify-center">
    <v-btn  variant="none" @click="showErrorCard = false">
      Ok
    </v-btn>
  </v-card-actions>
</v-card>


<v-layout class="d-flex flex-column align-center justify-center pa-16 h-100">

    <div>

      <!-- <h5 v-if="success && ticket">Ticket Fee: {{ ticket.PARKFEE }}</h5> -->
 
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

  <v-layout class="d-flex justify-space-between mt-2">
    <v-btn @click="submitPayment" color="blue-darken-4">
      Pay
    </v-btn>

  <v-btn @click ="cancelPayment" color="red-darken-2">Cancel</v-btn>

  </v-layout>
</v-card>

      <div v-else>
        <v-text-field
          class="text-h1"
          v-model="form.PLATENO"
          placeholder="Enter Plate No."
          style="width: 300px"
          variant="underlined"
        />
        <v-btn     :disabled="!form.PLATENO" size="x-large" @click="submitPlate" block color="blue-darken-4">
          Submit
        </v-btn>
      </div>

    </div>
  </v-layout>
</template>

<script setup>
import { useForm, usePage, } from '@inertiajs/vue3';
import { computed,onMounted ,ref,watch} from 'vue';
import dayjs from 'dayjs';
import { Link,router } from '@inertiajs/vue3'


const page = usePage();

const ticket = computed(() => page.props.ticket || null);
const success = computed(() => page.props.success || false);
const errorCardMsg = ref('');
const showErrorCard = ref(false);
const ticketId = ref(null);
const messages = ref([]);
const text = ref('hhh')
// setting ID to use tot form2id
watch(ticket, (newTicket) => {
  if (newTicket) {
    ticketId.value = newTicket.id
  }
})

// onMounted(() => {
//   const msg = page.props.flash.success;
//   if (msg) {
//     messages.value.push({
//       color: 'green',
//       icon: 'mdi-check-circle',
//       message: msg,
//       timeout: 3000,
//     });
//   }
// });


const now = dayjs(); // current timestam

const formatDate = (date) => {
  return dayjs(date).format("MM/DD/YYYY")
}

const formatFee = (fee) => {
  return `${Number(fee).toFixed(2)} PHP`
}

const form = useForm({
  PLATENO: '',
    PARKOUTYEAR: now.year(),
    PARKOUTMONTH: now.month() + 1, // months are 0-indexed
    PARKOUTDAY: now.date(),
    PARKOUTHOUR: now.hour(),
    PARKOUTMINUTE: now.minute(),
    PARKOUTSECOND: now.second(),

});



const form2 = useForm({
  ID:  ticketId.value || null
})


const clearError = () => {
  errorCardMsg.value = ''
  showErrorCard.value = false
}


const cancelPayment = ()=>{
  router.visit(route('parkout'));
}

const submitPlate = () => {
clearError();
form.post('/submit/parkout', {
            onSuccess:()=>{
         form.reset();   
    },
  onError: (errors) => {
    if (errors.PLATENO) {
      showErrorCard.value = true;
      errorCardMsg.value = errors.PLATENO;
    }
  }
});

};



const submitPayment = () =>{
 form2.ID = ticketId.value
  clearError();
  form2.post('/submit/payment',{
    onSuccess:()=>{
   clearError();  
     messages.value.push({
        icons: 'mdi-check-circle',
        text: 'Payment successful!',
        timeout: 3000,
      });
    },
    onError: (errors)=>{
        if (errors.ID) {
      showErrorCard.value = true;
      errorCardMsg.value = 'Payment failed. Please try again.'
    }
    }


  })

   

}



</script>
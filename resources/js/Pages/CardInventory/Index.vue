<template>
    <v-container>
        <div class="d-flex justify-end mb-4">
        <!-- Open dialog -->
            <v-btn color="success" @click="addCard">
            Generate Card
            </v-btn>
        </div>

 
    <Create v-model="showDialog" :cardTemplate = "cardTemplate" />

    <!-- Table -->
        <v-card title="Card Inventory" class="mt-4" >
            <v-data-table
            :headers="headers"
            :items="cardDetail"
            class="elevation-1"
            hide-default-footer
            :items-per-page="cardDetail.length"
            >
                <template v-slot:item.status="{ item }">
                <span :class=" item.status == 'AVAILABLE'? 'bg-green-lighten-5 text-green-lighten-1 pa-1':  'bg-blue-lighten-5 text-blue-lighten-1 pa-1'">{{ item.status}}</span>
                </template> 

                <template v-slot:item.created_at="{ item }">
                {{ formatDate(item.created_at) }}
                </template>

                <template v-slot:item.qr_code_hash="{ item }">
                    <img :src="qrCodeMap[item.id]" alt="QR Code" width="80" />
                </template> 

                <template v-slot:item.download="{ item }">
                    <v-btn
                    icon="mdi-download"
                    color="primary"
                    size="small"
                    @click="downloadQRCode(item)"
                    ></v-btn>
                </template>

            <!-- Slot for actions -->
            <!-- <template  v-slot:item.actions="{ item }"> -->
                <!-- <v-btn
                icon="mdi-pencil"
                color="primary"
                size="small"
                @click="editItem(item)"
                ></v-btn>

                <v-btn
                icon="mdi-delete"
                color="error"
                size="small"
                @click="deleteItem(item)"
                ></v-btn> -->
            <!-- </template> -->
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script setup>
import { ref,computed,onMounted} from 'vue'
import dayjs from 'dayjs'
import Create from './Create.vue'
import { usePage } from '@inertiajs/vue3'
import QRCode from 'qrcode'

// ✅ Dialog open/close state
const showDialog = ref(false)

// ✅ Table headers (must match your DB columns!)
const headers = [
   { key: 'id', title: 'ID' },
   { key: 'qr_code_hash', title: 'QR Code' },
  { key: 'card_name', title: 'Card Name' },
  { key: 'no_of_days', title: 'No. of Days' },
  { key: 'price', title: 'price' },
  { key: 'discount', title: 'Discount' },
  { key: 'amount', title: 'Amount' },
  { key: 'balance', title: 'Balance' },
  { key: 'status', title: 'Status' },
  { key: 'created_at', title: 'Date Created' },
   { key: 'download', title: 'Download' }, // ✅ Add this
  // { key: 'actions', title: 'Actions' },
]

// ✅ Grab server data from Inertia props
const page = usePage()
const cardTemplate = computed(() => page.props.cardTemplate)
const cardDetail = computed(()=> page.props.cardDetail)
const selectedTemplate = ref(null)

// const spanClass = computed(()=> {
//  if (item.status.value =='AVAILABLE')
//  return "bg-green-lighten-5 text-green-lighten-1 pa-1";
// else{
//   return "bg-blue-lighten-5 text-green-blue-1 pa-1";
// }
// })

const formatDate = (date) => {
  return date ? dayjs(date).format('MM/DD/YYYY') : ''
}


const qrCodeMap = ref({})

onMounted(async () => {
  for (const detail of cardDetail.value) {
    if (detail.qr_code_hash) {
      qrCodeMap.value[detail.id] = await QRCode.toDataURL(detail.qr_code_hash)
    }
  }
})




// Handlers
const editItem = (item) => {
selectedTemplate.value = item;
  showDialog.value = true;
}

const deleteItem = (item) => {
  console.log('Delete', item)
}

const addCard = () => {
 
//   selectedTemplate.value = null   // reset for "Add Template"
  showDialog.value = true
}

const downloadQRCode = (item) => {
  const qrDataUrl = qrCodeMap.value[item.id]
  if (!qrDataUrl) return

  // Create a temporary <a> tag
  const link = document.createElement('a')
  link.href = qrDataUrl
  link.download = `${item.card_name || 'qrcode'}-${item.id}.png` // filename
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}




</script>

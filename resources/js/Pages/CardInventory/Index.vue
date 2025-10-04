<template>
  <v-container>
    <div class="d-flex justify-end mb-4">
      <v-btn color="success"  @click="addCard">
        Generate Card
      </v-btn>
    </div>

    <Create v-model="showDialog" :cardTemplate="cardTemplate" v-model:loading="showLoadingDialog" v-model:progress="progress" />
    <Transactions  v-if = "showTransactionsDialog"  v-model="showTransactionsDialog"  :selectedCard = "selectedCard" />

     <v-dialog   v-model="showLoadingDialog" max-width="320" persistent>
      <v-list class="bg-grey-darken-4" elevation="12" rounded="lg">
        <v-list-item  title="Generating Cards...">
     <template v-slot:append>
            <v-progress-circular
              color="primary"
              indeterminate="disable-shrink"
              size="16"
              width="2"
            ></v-progress-circular>
          </template>
          </v-list-item>
      </v-list>
    </v-dialog>

    <v-card title="Card Inventory" class="mt-4">
      <v-data-table-server
        :headers="headers"
        :items="cardDetail.data"
        v-model:items-per-page="itemsPerPage"
        :page="pageNumber"
        :items-length="cardDetail.total"
        class="elevation-1"
        @update:page="goToPage"
      >
<template v-slot:top>
  <v-row class="pa-2" justify="between">
    <!-- 🔍 Search field -->
    <v-col cols="12" sm="3" md="3">
      <v-text-field
        v-model="filters.cardNumber"
        prepend-inner-icon="mdi-magnify"
        label="Search card number"
        density="compact"
        hide-details="auto"
        variant="outlined"
      />
    </v-col>

    <!-- 📅 Date range + filter button -->
    <v-col cols="12" sm="9" md="9">
      <v-row>
        <v-col cols="12" sm="4">
          <v-date-input
            v-model="filters.startDate"
            prepend-icon=""
            prepend-inner-icon="$calendar"
            label="Start Date"
            density="compact"
            hide-details="auto"
            variant="underlined"
          />
        </v-col>

        <v-col cols="12" sm="4">
          <v-date-input
            v-model="filters.endDate"
            prepend-icon=""
            prepend-inner-icon="$calendar"
            label="End Date"
            density="compact"
            hide-details="auto"
            variant="underlined"
          />
        </v-col>

        <v-col cols="12" sm="4" class="d-flex align-center">
          <v-btn
            color="indigo-darken-4"
            @click="applyDateFilter"
            prepend-icon="mdi-magnify"
            block
          >
            Search
          </v-btn>
        </v-col>
      </v-row>
    </v-col>
  </v-row>
</template>


        <template v-slot:item.price="{ item }">
          {{ formatCurrency(item.price) }}
        </template>

        <template v-slot:item.discount="{ item }">
          {{ formatCurrency(item.discount) }}
        </template>

        <template v-slot:item.amount="{ item }">
          {{ formatCurrency(item.amount) }}
        </template>

            <template v-slot:item.balance="{ item }">
          {{ formatCurrency(item.balance) }}
        </template>

        <template v-slot:item.status="{ item }">
        <span
            :class="
            item.status === 'Available'
                ? 'bg-green-lighten-5 text-green-darken-1 pa-1'
                : item.status === 'Consumed'
                ? 'bg-red-lighten-5 text-red-darken-1 pa-1'
                : 'bg-yellow-lighten-5 text-yellow-darken-4 pa-1'
            "
        >
            {{ item.status }}
        </span>
        </template>


        <template v-slot:item.created_at="{ item }">
          {{ formatDate(item.created_at) }}
        </template>

        <template v-slot:item.qr_code_hash="{ item }">
          <img :src="qrCodeMap[item.id]" alt="QR Code" width="80" />
        </template>

           <template v-slot:item.transactions="{ item }">
          <v-btn
            size="small"
                 color="indigo-darken-4"
              icon="mdi-eye-outline"
               variant="text"
            @click="viewTransactions(item)"
          ></v-btn>
        </template>

        <template v-slot:item.download="{ item }">
          <v-btn
          color="indigo-darken-4"
            icon="mdi-download"
            variant="text"
            size="small"
            @click="downloadQRCode(item)"
          ></v-btn>
        </template> 

        <!-- Hidden download card template -->
<!-- 
  <template v-slot:bottom>
    <v-divider></v-divider>
    <v-row class="px-4 py-2" justify="end">
      <v-col cols="auto" class="text-right font-weight-bold">
        Total Amount: {{ formatCurrency(totalAmount) }}
      </v-col>
    </v-row>
  </template> -->
<template v-slot:body.append>
  <tr class=" bg-gray-200">
    <td  class="text-left font-weight-bold">Total:</td>
    <td  ></td>
    <td class="font-semibold">{{ formatCurrency(totalPrice) }}</td>
     <td class="font-semibold" >{{ formatCurrency(totalDiscount) }}</td>
    <td class="font-semibold">{{ formatCurrency(totalAmount) }}</td>
    <td class="font-semibold">{{ formatCurrency(totalBalance) }}</td>
    <td  ></td>
     <td  ></td>
    <td  ></td>
     <td  ></td>
  </tr>
</template>


      </v-data-table-server>
    </v-card>

    <div
  v-if="cardToDownload"
  id="download-card"
  class="absolute -top-[9999px] -left-[9999px] w-[300px] bg-white text-center rounded-xl shadow-lg p-6 flex flex-col items-center space-y-3 border border-gray-200"
>
  <h3 class="text-xl font-semibold text-gray-800">
    {{ cardToDownload.card_number }}
  </h3>

  <img
    :src="qrCodeMap[cardToDownload.id]"
    alt="QR"
    class="w-36 h-36 my-2"
  />

  <div class="text-left w-full space-y-1">
    <p class="text-gray-700 text-sm">
      <span class="font-bold">Amount:</span> {{ formatCurrency(cardToDownload.amount) }}
    </p>
    <p class="text-gray-700 text-sm">
      <span class="font-bold">Balance:</span> {{ formatCurrency(cardToDownload.balance) }}
    </p>
    <p
      class="text-sm font-bold"
      :class="{
        'text-green-600': cardToDownload.status === 'Available',
        'text-red-600': cardToDownload.status === 'Consumed',
        'text-yellow-600': cardToDownload.status === 'Pending'
      }"
    >
      Status: {{ cardToDownload.status }}
    </p>
  </div>

  <p class="text-xs text-gray-400 mt-4">
    Generated: {{ formatDate(cardToDownload.created_at) }}
  </p>
</div>

  </v-container>

  

   
</template>

<script setup>
import { ref, computed, watch ,reactive,nextTick} from 'vue';
import dayjs from 'dayjs';
import Create from './Create.vue';
import { usePage, router } from '@inertiajs/vue3';
import QRCode from 'qrcode';
import { formatCurrency } from '../../utils/utility';
import Transactions from './Transactions.vue';
import html2canvas from 'html2canvas';



// Dialog state
const showDialog = ref(false);
const showLoadingDialog = ref(false); 
const showTransactionsDialog = ref(false);
const sellCardPassinfo = reactive({});
const selectedCard = ref({});
const cardToDownload = ref(null);

const progress = ref(0);

// Table headers
const headers = [
  // { key: 'id', title: 'ID' },
  // { key: 'qr_code_hash', title: 'QR Code' },
  { key: 'card_number', title: 'Card Number' },
  { key: 'no_of_days', title: 'No. of Days' },
  { key: 'price', title: 'Price' },
  { key: 'discount', title: 'Discount' },
  { key: 'amount', title: 'Amount' },
  { key: 'balance', title: 'Balance' },
  { key: 'status', title: 'Status' },
  { key: 'created_at', title: 'Date Created' },
  { key: 'transactions', title: 'Transactions', align: 'center' },
  // Align the 'Download' column to the center
  { key: 'download', title: 'Download', align: 'center' },
];

// Inertia props
const page = usePage();
const cardTemplate = computed(() => page.props.cardTemplate);
const cardDetail = computed(() => page.props.cardDetail);


// Date filters
const filters = ref({

  cardNumber : '',
  startDate: new Date(),
 endDate: new Date(),
});

const sell_card_info = ()=>{


}

const pageNumber = ref(cardDetail.value.current_page);

const itemsPerPage = ref(cardDetail.value.per_page);

const totalAmount = computed(() => {
  if (!cardDetail.value?.data) return 0
  return cardDetail.value.data.reduce((sum, row) => sum + Number(row.amount || 0), 0)
})

const totalBalance = computed(() => {
  if (!cardDetail.value?.data) return 0
  return cardDetail.value.data.reduce((sum, row) => sum + Number(row.balance || 0), 0)
})

const totalPrice = computed(() => {
  if (!cardDetail.value?.data) return 0
  return cardDetail.value.data.reduce((sum, row) => sum + Number(row.price || 0), 0)
})

const totalDiscount = computed(() => {
  if (!cardDetail.value?.data) return 0
  return cardDetail.value.data.reduce((sum, row) => sum + Number(row.discount || 0), 0)
})



watch(cardDetail, (newVal) => {
  if (newVal) {
    pageNumber.value = newVal.current_page;
  }
});

// QR code map
const qrCodeMap = ref({});


watch(
  cardDetail,
  async (newCards) => {
    if (!newCards || !newCards.data) return;

    for (const detail of newCards.data) {
      if (detail.qr_code_hash && !qrCodeMap.value[detail.id]) {
        qrCodeMap.value[detail.id] = await QRCode.toDataURL(
          detail.qr_code_hash
        );
      }
    }
  },
  { immediate: true }
);

const applyDateFilter = () => {

  const formattedStartDate = filters.value.startDate
    ? dayjs(filters.value.startDate).format('YYYY-MM-DD')
    : null;
  const formattedEndDate = filters.value.endDate
    ? dayjs(filters.value.endDate).format('YYYY-MM-DD')
    : null;

  router.get(
    route('card-inventory.index'),
    {
        card_number: filters.value.cardNumber,
      dateFrom: formattedStartDate,
      dateTo: formattedEndDate,
      page: 1, // reset to first page when filtering
    },
    { preserveState: true, replace: true }
  );
};

// Date formatting
const formatDate = (date) =>
  date ? dayjs(date).format('MM/DD/YYYY') : '';

// Handlers
const addCard = () => {
  showDialog.value = true;
};

const goToPage = (pageNumber) => {
  router.get(
    route('card-inventory.index'),
    { 
      page: pageNumber,
    //   card_number: filters.value.cardNumber,
    //   dateFrom: dayjs(filters.value.startDate).format('YYYY-MM-DD'),
    //   dateTo: dayjs(filters.value.endDate).format('YYYY-MM-DD'),
    },
    { preserveState: true, replace: true }
  );
};
// const downloadQRCode = (item) => {
//   const qrDataUrl = qrCodeMap.value[item.id];
//   if (!qrDataUrl) return;

//   const link = document.createElement('a');
//   link.href = qrDataUrl;
//   link.download = `${item.card_name || 'qrcode'}-${item.id}.png`;
//   document.body.appendChild(link);
//   link.click();
//   document.body.removeChild(link);
// };


const downloadQRCode = async (item) => {
  cardToDownload.value = item;

  // wait for the hidden card to render
  await nextTick();

  const cardElement = document.getElementById('download-card');
  if (!cardElement) {
    console.error('❌ Card element not found');
    return;
  }

  try {
    const canvas = await html2canvas(cardElement, {
      backgroundColor: '#ffffff',
      scale: 2,
      useCORS: true, // very important if QR image is a DataURL or external
    });

    const imageURL = canvas.toDataURL('image/png');

    const link = document.createElement('a');
    link.href = imageURL;
    link.download = `${item.card_number || 'card'}-${item.id}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    cardToDownload.value = null;
  } catch (err) {
    console.error('⚠️ Failed to download card:', err);
  }
};

const viewTransactions = (item)=>{

    showTransactionsDialog.value = true;
    // sellCardPassinfo = {
    //     card_name : item.card_name,
    //     id : item.id,
    //     card_number : item.card_number


    // }

    selectedCard.value = item;

}
</script>

<!-- <style scoped>
:deep(.v-data-table table) {
  border-collapse: separate !important;
  border-spacing: 0 12px; /* horizontal vertical */
}

:deep(.v-data-table tbody tr) {
    margin-bottom: 5px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 10px 10px rgba(0,0,0,0.06);
}
</style> -->
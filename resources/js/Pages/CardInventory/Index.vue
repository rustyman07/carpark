<template>
  <div class="inventory-wrapper">
    <v-container class="py-8 px-6">
      <!-- Page Header -->
      <div class="page-header mb-8">
        <div>
          <h1 class="text-h4 font-weight-bold text-indigo-darken-4 mb-2">
            <v-icon size="32" class="mr-2">mdi-card-account-details</v-icon>
            Card Inventory
          </h1>
          <p class="text-body-1 text-medium-emphasis">
            Manage and track all parking cards
          </p>
        </div>


        <v-btn
          v-if="user.role === 1"
          color="indigo-darken-4"
          size="large"
          @click="addCard"
          prepend-icon="mdi-plus-circle"
          class="generate-btn elevation-4"
        >
          Generate Card
        </v-btn>
      </div>


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

      <!-- Stats Summary Cards -->
      <!-- <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="success">mdi-check-circle</v-icon>
                <v-chip size="small" color="success" variant="flat">Active</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ cardDetail.data?.filter(c => c.status === 'Available').length || 0 }}
              </h3>
              <p class="text-caption text-medium-emphasis">Available Cards</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="error">mdi-close-circle</v-icon>
                <v-chip size="small" color="error" variant="flat">Used</v-chip>
              </div>
              <h3 class="text-h5 font-weight-bold text-indigo-darken-4">
                {{ cardDetail.data?.filter(c => c.status === 'Consumed').length || 0 }}
              </h3>
              <p class="text-caption text-medium-emphasis">Consumed Cards</p>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card elevation-4" rounded="lg">
            <div class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-icon size="32" color="primary">mdi-cash-multiple</v-icon>
                <v-chip size="small" color="primary" variant="flat">Revenue</v-chip>
              </div>
              <h3 class="text-h6 font-weight-bold text-indigo-darken-4">
                {{ formatCurrency(totalAmount) }}
              </h3>
              <p class="text-caption text-medium-emphasis">Total Amount</p>
            </div>
          </v-card>
        </v-col>


        <v-col cols="12" sm="6" md="3">
  <v-card class="stat-card elevation-4" rounded="lg">
    <div class="pa-4">
      <div class="d-flex align-center justify-space-between mb-2">
        <v-icon size="32" color="orange">mdi-cash-register</v-icon>
        <v-chip size="small" color="orange" variant="flat">Sold</v-chip>
      </div>
      <h3 class="text-h6 font-weight-bold text-indigo-darken-4">
        {{ formatCurrency(totalSold) }}
      </h3>
      <p class="text-caption text-medium-emphasis">Total Sold</p>
    </div>
  </v-card>
</v-col>

      </v-row>  -->

      <!-- Dialogs -->
      <Create 
        v-model="showDialog" 
        :cardTemplate="cardTemplate" 
        v-model:loading="showLoadingDialog" 
        v-model:progress="progress" 
      />
      
      <Transactions  
        v-if="showTransactionsDialog"  
        v-model="showTransactionsDialog"  
        :selectedCard="selectedCard" 
      />

      <!-- Confirmation Dialog - Redesigned with Indigo Theme -->
      <v-dialog v-model="showConfirmDialog" max-width="500" persistent>
        <v-card rounded="lg" class="confirm-card">
          <!-- Header -->
          <div class="form-header pa-6 pb-4">
            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <v-avatar color="indigo-darken-4" size="48" class="mr-3">
                  <v-icon size="28" color="white">mdi-check-decagram</v-icon>
                </v-avatar>
                <div>
                  <h2 class="text-h6 font-weight-bold text-indigo-darken-4">
                    Confirm Sale
                  </h2>
                  <p class="text-caption text-medium-emphasis mb-0">
                    Mark card as sold in the system
                  </p>
                </div>
              </div>
              <v-btn
                icon="mdi-close"
                variant="text"
                @click="cancelConfirm"
              ></v-btn>
            </div>
          </div>

          <v-divider></v-divider>

          <!-- Content -->
          <v-card-text class="pa-6">
            <v-alert
              type="info"
              variant="tonal"
              density="comfortable"
              class="sale-alert mb-0"
            >
              <div class="text-body-2">
                You are about to mark card 
                <span class="font-weight-bold text-indigo-darken-4">{{ selectedCardToConfirm?.card_number }}</span> 
                as <span class="font-weight-bold">CONFIRMED</span>.
              </div>
              <div class="text-caption text-medium-emphasis mt-2">
                <v-icon size="16" class="mr-1">mdi-information-outline</v-icon>
                This action will update the card's status permanently.
              </div>
            </v-alert>
          </v-card-text>

          <v-divider></v-divider>

          <!-- Actions -->
          <v-card-actions class="pa-6">
            <v-spacer />
            <v-btn
              variant="outlined"
              size="large"
              @click="cancelConfirm"
              prepend-icon="mdi-close"
            >
              Cancel
            </v-btn>
            <v-btn
              color="indigo-darken-4"
              variant="flat"
              size="large"
              @click="confirmStatusChange"
              prepend-icon="mdi-check-circle"
            >
              Confirm Sale
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Main Data Table Card -->
      <v-card class="data-table-card elevation-4" rounded="lg">
        <!-- Filters Section -->
        <div class="filters-section pa-4">
          <v-row>
                <v-col cols="12" md="3">
                <v-text-field
                    v-model="filters.cardNumber"
                    prepend-inner-icon="mdi-magnify"
                    label="Search card number"
                    density="comfortable"
                    hide-details="auto"
                    variant="outlined"
                    bg-color="white"
                    clearable
                />
                </v-col>
                <v-col cols="12" sm="2">
                  <v-date-input
                    v-model="filters.startDate"
                    prepend-icon=""
                    prepend-inner-icon="$calendar"
                    label="Start Date"
                    density="comfortable"
                    hide-details="auto"
                    variant="outlined"
                    bg-color="white"
                  />
                </v-col>

                <v-col cols="12" sm="2">
                  <v-date-input
                    v-model="filters.endDate"
                    prepend-icon=""
                    prepend-inner-icon="$calendar"
                    label="End Date"
                    density="comfortable"
                    hide-details="auto"
                    variant="outlined"
                    bg-color="white"
                  />
                </v-col>
                <v-col cols="12" sm="3" >
                <v-select
                    v-model="filters.status"
                    :items="['All', 'Available', 'Sold','Confirmed','Consumed']"
                    label="Status"
                    density="comfortable"
                    variant="outlined"
                    bg-color="white"
                    hide-details
                           prepend-inner-icon="mdi-filter"
                />
            </v-col>
                 <v-col cols="12" sm="2">
              <v-btn
                color="indigo-darken-4"
                size="large"
                block
                  @click="applyDateFilter"
                prepend-icon="mdi-magnify"
              >
                Search
              </v-btn>
            </v-col>
            
            <!-- <v-col cols="12" md="2" class="text-right">
              <v-menu>
                <template v-slot:activator="{ props }">
                  <v-btn
                    v-bind="props"
                    color="indigo-darken-4"
                    variant="outlined"
                    prepend-icon="mdi-download"
                  >
                    Export
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item @click="exportData('excel')">
                    <template v-slot:prepend>
                      <v-icon>mdi-file-excel</v-icon>
                    </template>
                    <v-list-item-title>Export to Excel</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="exportData('pdf')">
                    <template v-slot:prepend>
                      <v-icon>mdi-file-pdf-box</v-icon>
                    </template>
                    <v-list-item-title>Export to PDF</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </v-col> -->
          </v-row>
        </div>

        <v-divider></v-divider>

        <!-- Data Table -->
        <v-data-table
          :headers="headers"
          :items="cardDetail"
           hide-default-footer
          class="custom-data-table"
          @update:page="goToPage"
        >

            <template v-slot:item.card_number="{ item }">
            <span class="font-weight-bold text-indigo-darken-4">{{ item.card_number }}</span>
          </template>
          <template v-slot:item.price="{ item }">
            <span class="font-weight-medium">{{ formatCurrency(item.price) }}</span>
          </template>

          <template v-slot:item.discount="{ item }">
            <span class="text-success font-weight-medium">{{ formatCurrency(item.discount) }}</span>
          </template>

          <template v-slot:item.amount="{ item }">
            <span class="font-weight-bold text-indigo-darken-4">{{ formatCurrency(item.amount) }}</span>
          </template>

          <template v-slot:item.balance="{ item }">
            <span class="font-weight-medium">{{ formatCurrency(item.balance) }}</span>
          </template>

    <template v-slot:item.status="{ item }">
  <v-chip
    :color="
      item.status === 'Available'
        ? 'success'
        : item.status === 'Consumed'
        ? 'error'
        : item.status === 'Confirmed'
        ? 'primary'  // 🌤️ sky blue hex
        : 'warning'
    "
    variant="flat"
    size="small"
    class="font-weight-medium w-100 d-flex justify-center"
  >
    <v-icon start size="12">
      {{
        item.status === 'Available'
          ? 'mdi-check-circle'
          : item.status === 'Consumed'
          ? 'mdi-close-circle'
          : item.status === 'Confirmed'
          ? 'mdi-thumb-up'
          : 'mdi-clock-outline'
      }}
    </v-icon>
    {{ item.status }}
  </v-chip>
</template>


          <template v-slot:item.created_at="{ item }">
            <div class="d-flex align-center">
              <v-icon size="16" class="mr-1 text-medium-emphasis">mdi-calendar</v-icon>
              <span class="text-body-2">{{ formatDate(item.created_at) }}</span>
            </div>
          </template>

          <template v-slot:item.transactions="{ item }">
            <v-btn
              size="small"
              color="indigo-darken-4"
              icon="mdi-eye-outline"
              variant="tonal"
              @click="viewTransactions(item)"
            ></v-btn>
          </template>

          <template v-slot:item.download="{ item }">
            <v-btn
              color="success"
              icon="mdi-download"
              variant="tonal"
              size="small"
              @click="downloadQRCode(item)"
            ></v-btn>
          </template>

          <template v-slot:item.confirmation="{ item }">
            <v-checkbox
              :model-value="item.status === 'Confirmed'"
              :disabled="item.status !== 'Sold'"
              @update:model-value="(value) => handleCheckboxChange(item, value)"
              color="indigo-darken-4"
              hide-details
            ></v-checkbox>
          </template>

          <!-- Summary Footer -->
          <template v-slot:body.append>
            <tr class="summary-row">
                               <td colspan="2" class="text-left font-weight-bold text-indigo-darken-4">
                       No of Records: {{cardDetail.length }}
            </td>
                      
            <td></td>
              <td></td>
              <!-- <td class="font-weight-bold text-indigo-darken-4">{{ formatCurrency(totalPrice) }}</td>
              <td class="font-weight-bold text-success">{{ formatCurrency(totalDiscount) }}</td> -->
              <td class="text-right font-weight-bold text-indigo-darken-4">{{ formatCurrency(totalAmount) }}</td>
              <!-- <td class="font-weight-bold text-warning">{{ formatCurrency(totalBalance) }}</td> -->
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          </template>
        </v-data-table>
      </v-card>

      <!-- Hidden Download Card Template -->
<!-- <div
  v-if="cardToDownload"
  id="download-card"
  class="download-card-template"
>
  <div class="card-template-inner">

    <div class="card-logo-section">
      <img
        src="/images/comlogo.png"
        alt="Company Logo"
        class="company-logo"
      />
    </div>


    <div class="card-header-section">
      <h3 class="card-number">{{ cardToDownload.card_number }}</h3>
      <v-chip
        :color="
          cardToDownload.status === 'Available'
            ? 'success'
            : cardToDownload.status === 'Consumed'
            ? 'error'
            : 'warning'
        "
        size="small"
        variant="flat"
      >
        {{ cardToDownload.status }}
      </v-chip>
    </div>

 
    <div class="qr-section">
      <img :src="qrCodeMap[cardToDownload.id]" alt="QR Code" class="qr-image" />
    </div>

  
    <div class="card-details">
      <div class="detail-row">
        <span class="label">Amount</span>
        <span class="value">{{ formatCurrency(cardToDownload.amount) }}</span>
      </div>
      <div class="detail-row">
        <span class="label">Balance</span>
        <span class="value">{{ formatCurrency(cardToDownload.balance) }}</span>
      </div>
      <div class="detail-row">
        <span class="label">Days</span>
        <span class="value"></span>
      </div>
    </div>

    <div class="card-footer-section">
      <p class="generated-date">{{ formatDate(cardToDownload.created_at) }}</p>
    </div>
  </div>
</div> -->

<div 
v-if="cardToDownload"
id="download-card"
  class=" w-[270px] flex flex-col border  border-gray-300 rounded-lg bg-white"
>

<div class=" flex flex-col items-center  px-8 pt-8">
  <img :src="`/images/comlogo.png`" alt="Company Logo" class="w-[80px] " />
    <hr class="my-4 w-full border-t border-gray-100" />
      <span class="text-xl font-bold">{{ cardToDownload.card_name }}</span>
</div>

<div class=" p-3 flex items-center justify-center">
  <img :src="qrCodeMap[cardToDownload.id]" alt="QR Code" class="w-[120px]" />
</div>

  <div class="w-full text-xs bg-sky-800 flex flex-col gap-2 px-8 p-4">
    <div class="space-y-1 mt-2 text-white text-xs">
      <div class="flex justify-between">
        <span class="label ">CARD NUMBER</span>
        <span class="value font-semibold">{{ cardToDownload.card_number }}</span>
      </div>
      <div class="flex justify-between">
        <span class="label ">NO. OF DAYS</span>
        <span class="value font-semibold">{{ cardToDownload.no_of_days }}</span>
      </div>
      <div class="flex justify-between">
        <span class="label ">AMOUNT</span>
        <span class="value font-semibold">{{ cardToDownload.amount }}</span>
      </div>
    </div>
  </div>
</div>



<!-- <div 
  v-if="cardToDownload"
  id="download-card"
  class="w-[250px] flex  border border-gray-300 rounded-lg p-4 bg-white"
  
>
    <div class="w-50">
        <div class="qr-section">
            <img :src="qrCodeMap[cardToDownload.id]" alt="QR Code" class="qr-image" />
        </div>
    </div>
    <div class="w-50">
            <div class="detail-row">
        <span class="label">Amount</span>
        <span class="value">{{ formatCurrency(cardToDownload.amount) }}</span>
      </div>
    </div>
</div> -->




    </v-container>
  </div>
</template>

<script setup>
import { ref, computed, watch, reactive, nextTick } from 'vue';
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
const selectedCard = ref({});
const cardToDownload = ref(null);
const progress = ref(0);

const showConfirmDialog = ref(false);
const selectedCardToConfirm = ref(null);


// Inertia props
const page = usePage();
const cardTemplate = computed(() => page.props.cardTemplate);
const cardDetail = computed(() => page.props.cardDetail);
const user = computed(() => page.props.auth?.user || {});

const today = dayjs().format('YYYY-MM-DD')
const startOfMonth = dayjs().startOf('month').format('YYYY-MM-DD')
// Date filters
const filters = ref({
  cardNumber: '',
  startDate: startOfMonth,
  endDate: today,
  status: 'All'
});
const pageNumber = ref(cardDetail.value.current_page);
const itemsPerPage = ref(cardDetail.value.per_page);



console.log(user.value.role)
// Table headers
const headers = [
  { key: 'card_number', title: 'Card Number', sortable: true },
  { key: 'no_of_days', title: 'No. of Days', align: 'center' },
  { key: 'price', title: 'Price', align: 'end' },
  { key: 'discount', title: 'Discount', align: 'end' },
  { key: 'amount', title: 'Amount', align: 'end' },
  { key: 'balance', title: 'Balance', align: 'end' },
  { key: 'status', title: 'Status', align: 'center' },
  { key: 'created_at', title: 'Date Created' },
  { key: 'transactions', title: 'View', align: 'center', sortable: false },
  { key: 'download', title: 'Download', align: 'center', sortable: false },

];

if (user.value.role == 1) { // admin
  headers.push({ key: 'confirmation', title: 'Confirm', align: 'center' });
}
// Computed totals
const totalAmount = computed(() => {
  if (!cardDetail.value) return 0;
  return cardDetail.value.reduce((sum, row) => sum + Number(row.amount || 0), 0);
});

// const totalBalance = computed(() => {
//   if (!cardDetail.value?.data) return 0;
//   return cardDetail.value.data.reduce((sum, row) => sum + Number(row.balance || 0), 0);
// });

const totalSold = computed(() => {
  if (!cardDetail.value?.data) return 0;
  return cardDetail.value.data
    .filter((row) => row.status === 'Sold')
    .reduce((sum, row) => sum + Number(row.amount || 0), 0);
});




const totalPrice = computed(() => {
  if (!cardDetail.value?.data) return 0;
  return cardDetail.value.data.reduce((sum, row) => sum + Number(row.price || 0), 0);
});

const totalDiscount = computed(() => {
  if (!cardDetail.value?.data) return 0;
  return cardDetail.value.data.reduce((sum, row) => sum + Number(row.discount || 0), 0);
});

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
        qrCodeMap.value[detail.id] = await QRCode.toDataURL(detail.qr_code_hash);
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
      status: filters.value.status,
    },
    { preserveState: true, replace: true }
  );
};

const formatDate = (date) => (date ? dayjs(date).format('MM/DD/YYYY') : '');

const addCard = () => {
  showDialog.value = true;
};

const goToPage = (pageNumber) => {
  const formattedStartDate = filters.value.startDate
    ? dayjs(filters.value.startDate).format('YYYY-MM-DD')
    : null;
  const formattedEndDate = filters.value.endDate
    ? dayjs(filters.value.endDate).format('YYYY-MM-DD')
    : null;

  router.get(
    route('card-inventory.index'),
    {
      page: pageNumber,
      card_number: filters.value.cardNumber,
      dateFrom: formattedStartDate,
      dateTo: formattedEndDate,
    },
    { preserveState: true, replace: true }
  );
};


// Handle checkbox change
const handleCheckboxChange = (item, value) => {
  if (value && item.status !== 'Confirmed') {
    selectedCardToConfirm.value = item;
    showConfirmDialog.value = true;
  }
};

const cancelConfirm = () => {
  // Reset without confirming - checkbox will automatically uncheck
  selectedCardToConfirm.value = null;
  showConfirmDialog.value = false;
};

const confirmStatusChange = () => {
  if (!selectedCardToConfirm.value) return;

  router.put(
    route('card-inventory.update-status', selectedCardToConfirm.value.id),
    { status: 'Confirmed' },
    {
      onSuccess: () => {
        // Update local UI
        selectedCardToConfirm.value.status = 'Confirmed';
        showConfirmDialog.value = false;
        selectedCardToConfirm.value = null;
      },
      onError: (err) => {
        console.error('Failed to update status:', err);
        showConfirmDialog.value = false;
        selectedCardToConfirm.value = null;
      },
    }
  );
};

const downloadQRCode = async (item) => {
  try {
    // 1️⃣ Assign selected card to render
    cardToDownload.value = item;

    // 2️⃣ Ensure QR code is generated
    if (!qrCodeMap.value[item.id]) {
      qrCodeMap.value[item.id] = await QRCode.toDataURL(item.qr_code_hash);
    }

    // 3️⃣ Wait for Vue to update the DOM
    await nextTick();

    const cardElement = document.getElementById('download-card');
    if (!cardElement) {
      console.error('❌ Card element not found');
      return;
    }

    // 4️⃣ Wait for QR image and logo to fully load
    const imgs = cardElement.querySelectorAll('img');
    await Promise.all(
      Array.from(imgs).map((img) => {
        if (img.complete) return Promise.resolve();
        return new Promise((resolve) => {
          img.onload = resolve;
          img.onerror = resolve;
        });
      })
    );

    // 5️⃣ Temporarily hide the card off-screen (no flicker)
    const prevStyle = cardElement.style.cssText;
    cardElement.style.cssText = `
      position: fixed;
      top: -10000px;
      left: -10000px;
      opacity: 1;
      display: block;
      z-index: -1;
    `;

    // 6️⃣ Capture the card as an image
    const canvas = await html2canvas(cardElement, {
      backgroundColor: '#ffffff',
      scale: 2,
      useCORS: true,
    });

    // 7️⃣ Restore styles
    cardElement.style.cssText = prevStyle;

    // 8️⃣ Convert to PNG and trigger download
    const imageURL = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.href = imageURL;
    link.download = `${item.card_number || 'card'}-${item.id}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (err) {
    console.error('⚠️ Failed to download card:', err);
  } finally {
    // 9️⃣ Reset reactive card
    cardToDownload.value = null;
  }
};

const viewTransactions = (item) => {
  showTransactionsDialog.value = true;
  selectedCard.value = item;
};

const exportData = (format) => {
  console.log(`Exporting data as ${format}`);
  // Add export functionality here
};
</script>

<style scoped>
.inventory-wrapper {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.generate-btn {
  transition: all 0.3s ease;
}

.generate-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(26, 35, 126, 0.3) !important;
}

/* Stat Cards */
.stat-card {
  background: white;
  border-left: 4px solid transparent;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}

.stat-card:nth-child(1) {
  border-left-color: #4caf50;
}

.stat-card:nth-child(2) {
  border-left-color: #f44336;
}

.stat-card:nth-child(3) {
  border-left-color: #1a237e;
}

.stat-card:nth-child(4) {
  border-left-color: #ffa000;
}

/* Data Table Card */
.data-table-card {
  background: white;
  overflow: hidden;
}

.filters-section {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.03) 0%, rgba(26, 35, 126, 0.01) 100%);
}

/* Custom Data Table Styles */
:deep(.custom-data-table) {
  background: transparent;
}

:deep(.custom-data-table .v-table__wrapper) {
  background: white;
}

:deep(.custom-data-table thead) {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
}

:deep(.custom-data-table thead th) {
  color: white !important;
  font-weight: 600 !important;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

:deep(.custom-data-table tbody tr) {
  transition: background-color 0.2s ease;
}

:deep(.custom-data-table tbody tr:hover) {
  background: rgba(26, 35, 126, 0.04) !important;
}

/* Summary Row */



/* Confirmation Dialog - Indigo Theme */
.confirm-card {
  overflow: hidden;
}

.form-header {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.05) 0%, rgba(26, 35, 126, 0.02) 100%);
}

.sale-alert {
  border-left: 4px solid #1a237e;
}

/* Animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.confirm-card {
  animation: fadeIn 0.3s ease-out;
}




.download-card-template {
  position: absolute;
  top: -9999px;
  left: -9999px;
  width: 400px;
  background: white;
}

.card-template-inner {
  background: #ffffff;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid #f0f0f0;
}

.card-logo-section {
  text-align: center;
  margin-bottom: 24px;
}

.company-logo {
  width: 80px;
  height: auto;
}

.card-header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid #f0f0f0;
}

.card-number {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

.qr-section {
  text-align: center;
  margin-bottom: 24px;
}

.qr-image {
  width: 160px;
  height: 160px;
  border-radius: 8px;
}

.card-details {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
}

.detail-row .label {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

.detail-row .value {
  font-size: 16px;
  color: #1a1a1a;
  font-weight: 600;
}

.card-footer-section {
  text-align: center;
  padding-top: 16px;
  border-top: 1px solid #f0f0f0;
}

.generated-date {
  font-size: 12px;
  color: #999;
  margin: 0;
}

/* Responsive Design */
@media (max-width: 960px) {
  .page-header {
    text-align: center;
  }

  .page-header > div {
    width: 100%;
  }

  .generate-btn {
    width: 100%;
  }
}

/* Animations */
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

.v-card {
  animation: fadeInUp 0.5s ease-out;
}





</style>
<template>
  <v-container>
    <div class="d-flex justify-end mb-4">
      <v-btn color="success" @click="addCard">
        Generate Card
      </v-btn>
    </div>

    <Create v-model="showDialog" :cardTemplate="cardTemplate" />

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
          <v-row class="pa-2" justify="end">
            <v-col cols="12" sm="4" md="3">
              <v-date-input
                v-model="filters.startDate"
                      prepend-icon=""
      prepend-inner-icon="$calendar"
                label="Start Date"
                density="compact"
                hideDetails="auto"
                variant="underlined"
              />
            </v-col>

            <v-col cols="12" sm="4" md="3">
              <v-date-input
                v-model="filters.endDate"
                 prepend-icon=""
                prepend-inner-icon="$calendar"
                label="End Date"
                density="compact"
                hideDetails="auto"
                variant="underlined"
              />
            </v-col>

            <v-col cols="12" sm="4" md="2">
              <v-btn
                color="primary"
                @click="applyDateFilter"
                prepend-icon="mdi-magnify"
              >
                Filter
              </v-btn>
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

        <template v-slot:item.status="{ item }">
          <span
            :class="
              item.status === 'AVAILABLE'
                ? 'bg-green-lighten-5 text-green-lighten-1 pa-1'
                : 'bg-blue-lighten-5 text-blue-lighten-1 pa-1'
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

        <template v-slot:item.download="{ item }">
          <v-btn
            icon="mdi-download"
            color="primary"
            size="small"
            @click="downloadQRCode(item)"
          ></v-btn>
        </template>
      </v-data-table-server>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import dayjs from 'dayjs';
import Create from './Create.vue';
import { usePage, router } from '@inertiajs/vue3';
import QRCode from 'qrcode';
import { formatCurrency } from '../../utils/utility';

// Dialog state
const showDialog = ref(false);

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
  { key: 'download', title: 'Download' },
];

// Inertia props
const page = usePage();
const cardTemplate = computed(() => page.props.cardTemplate);
const cardDetail = computed(() => page.props.cardDetail);

// Date filters
const filters = ref({
  // v-date-input returns a Date object, so we'll use null as the initial value.
  startDate: new Date(),
  endDate: new Date(),
});

// ✅ Local page ref so Vuetify can update it
const pageNumber = ref(cardDetail.value.current_page);

const itemsPerPage = ref(cardDetail.value.per_page);

// Keep pageNumber in sync when Laravel paginator updates
watch(cardDetail, (newVal) => {
  if (newVal) {
    pageNumber.value = newVal.current_page;
  }
});

// QR code map
const qrCodeMap = ref({});

// Watch cardDetail for changes and generate QR codes
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
  // Format the dates to a string that Laravel's backend can understand
  const formattedStartDate = filters.value.startDate
    ? dayjs(filters.value.startDate).format('YYYY-MM-DD')
    : null;
  const formattedEndDate = filters.value.endDate
    ? dayjs(filters.value.endDate).format('YYYY-MM-DD')
    : null;

  router.get(
    route('card-inventory.index'),
    {
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
    { page: pageNumber },
    { preserveState: true }
  );
};

const downloadQRCode = (item) => {
  const qrDataUrl = qrCodeMap.value[item.id];
  if (!qrDataUrl) return;

  const link = document.createElement('a');
  link.href = qrDataUrl;
  link.download = `${item.card_name || 'qrcode'}-${item.id}.png`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};
</script>
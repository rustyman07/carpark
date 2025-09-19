<template>
  <v-card>
    <v-layout style="min-height: 100vh;">
      <v-app-bar color="blue-darken-4">
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title>Carpark</v-toolbar-title>
      </v-app-bar>

      <v-snackbar
        v-model="showSuccess"
        color="green-darken-2"
        timeout="3000"
        location="top"
        variant="flat"
      >
        <div class="d-flex align-center">
          <v-icon start icon="mdi-check-circle" class="me-2"></v-icon>
          <span>{{ successMessage }}</span>
        </div>
      </v-snackbar>

      <v-dialog v-model="showErrorCard" max-width="400" persistent>
        <v-card elevation="16">
          <v-card-title class="text-h6">Error</v-card-title>
          <v-card-text>{{ errorCardMsg }}</v-card-text>
          <v-card-actions class="justify-center">
            <v-btn variant="flat" color="primary" @click="showErrorCard = false">Ok</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-navigation-drawer v-model="drawer" :location="$vuetify.display.mobile ? 'left' : undefined" temporary>
        <v-list>
          <template v-for="item in items" :key="item.title">
            <v-list-group v-if="item.children" :value="item.value">
              <template v-slot:activator="{ props }">
                <v-list-item v-bind="props" :title="item.title" @click="navigate(item)" color="blue-darken-4">
                  <template v-slot:prepend>
                    <v-icon :icon="item.icon"></v-icon>
                  </template>
                </v-list-item>
              </template>
              <v-list-item
                v-for="child in item.children"
                :key="child.title"
                :title="child.title"
                @click="navigate(child)"
                :active="child.value === activeItem"
                color="blue-darken-4"
              >
                <template v-slot:prepend>
                  <v-icon :icon="child.icon"></v-icon>
                </template>
              </v-list-item>
            </v-list-group>
            <v-list-item
              v-else
              :title="item.title"
              @click="navigate(item)"
              :active="item.value === activeItem"
              color="blue-darken-4"
            >
              <template v-slot:prepend>
                <v-icon :icon="item.icon"></v-icon>
              </template>
            </v-list-item>
          </template>
        </v-list>
      </v-navigation-drawer>

      <v-main class="flex-1 bg-grey-lighten-5">
        <slot />
      </v-main>
    </v-layout>
  </v-card>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { route } from 'ziggy-js';
import { router, usePage } from '@inertiajs/vue3';

const drawer = ref(false);
const page = usePage();

const items = [
  { title: 'Home', value: 'home', route: 'home', icon: 'mdi-home' },
  { title: 'Park In', value: 'park_in', route: 'parkin.index', icon: 'mdi-parking' },
  { title: 'Park Out', value: 'park_out', route: 'parkout', icon: 'mdi-car-key' },
  { title: 'Card Template', route: 'card-template.index', value: 'card_template', icon: 'mdi-list-box-outline' },
  { title: 'Card Inventory', route: 'card-inventory.index', value: 'card_inventory', icon: 'mdi-list-box-outline' },
    { title: 'Sell Card', route: 'sell-card.create', value: 'sell_card', icon: 'mdi-list-box-outline' },
  { title: 'Logs', value: 'logs', route: 'logs', icon: 'mdi-history' },
  { title: 'Sales Person', value: 'sales_person', route: 'sales-person.index', icon: 'mdi-account' },
  {
    title: 'Transaction',
    value: 'park_out',
    route: '',
    icon: 'mdi-cash',
    children: [
      { title: 'Ticket Payments', value: 'ticket_payments', route: 'ticket.payments', icon: 'mdi-credit-card-settings' },
      { title: 'Card Payments', route: 'card-payments', value: 'card_payments', icon: 'mdi-list-box-outline' },
    ],
  },
  {
    title: 'Settings',
    value: 'settings',
    route: 'settings.index',
    icon: 'mdi-cog',
    children: [{ title: 'Company', value: 'company_card_template', route: 'company.index', icon: 'mdi-credit-card-settings' }],
  },
];

const activeItem = computed(() => {
  const currentRouteName = page.props.ziggy.currentRouteName;
  for (const item of items) {
    if (item.route === currentRouteName) {
      return item.value;
    }
    if (item.children) {
      const foundChild = item.children.find((child) => child.route === currentRouteName);
      if (foundChild) {
        return foundChild.value;
      }
    }
  }
  return 'home';
});

function navigate(item) {
  if (item.route) {
    router.visit(route(item.route));
  }
}

const errorCardMsg = ref('');
const showErrorCard = ref(false);

const successMessage = ref('');
const showSuccess = ref(false);

// Handler for popstate event
const handlePopState = () => {
    sessionStorage.setItem('isBackNavigation', 'true');
};

onMounted(() => {
    // Attach listener when the component is mounted
    window.addEventListener('popstate', handlePopState);
});

onUnmounted(() => {
    // Remove listener when the component is unmounted to prevent memory leaks
    window.removeEventListener('popstate', handlePopState);
});

watch(
  () => page.props.flash.success,
  (val) => {
    // Check if we're navigating back or forward from the history
    if (sessionStorage.getItem('isBackNavigation') === 'true') {
        sessionStorage.removeItem('isBackNavigation');
        return;
    }
    
    if (val) {
      showSuccess.value = true;
      successMessage.value = val;
    }
  },
  { deep: true, immediate: true }
);

watch(
  () => page.props.errors,
  (val) => {
    if (val) {
      if (val.PLATENO) {
        showErrorCard.value = true;
        errorCardMsg.value = val.PLATENO;
      }
      if (val.qr_code) {
        showErrorCard.value = true;
        errorCardMsg.value = val.qr_code;
      }
    }
  },
  { immediate: true }
);

watch(
  () => page.props.flash?.error,
  (val) => {
    if (sessionStorage.getItem('isBackNavigation') === 'true') {
        sessionStorage.removeItem('isBackNavigation');
        return;
    }

    if (val) {
      showErrorCard.value = true;
      errorCardMsg.value = val;
    }
  },
  { deep: true, immediate: true }
);

</script>
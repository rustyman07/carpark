<template>
  <v-layout style="min-height: 100vh">
    <!-- Enhanced App Bar -->
    <v-app-bar 
      elevation="4" 
      class="app-bar-custom"
      color="indigo-darken-4"
    >
      <v-app-bar-nav-icon 
        variant="text" 
        @click.stop="drawer = !drawer"
        class="nav-icon"
      ></v-app-bar-nav-icon>
      
      <v-toolbar-title class="d-flex align-center">
        <v-icon size="32" class="mr-2">mdi-parking</v-icon>
        <span class="toolbar-title">Carpark </span>
      </v-toolbar-title>
      
      <v-spacer></v-spacer>

      <!-- User Info + Shift Buttons -->
      <div class="user-section mr-4" v-if="props.auth.user.name">
        <v-chip
          color="white"
          variant="flat"
          size="default"
          class="user-chip mr-4"
        >
          <v-avatar start color="indigo-lighten-2" size="32">
            <v-icon size="20">mdi-account</v-icon>
          </v-avatar>
          <span class="font-weight-medium">{{ props.auth.user.name }}</span>
        </v-chip>

        <!-- ✅ Show only for staff (role 2) -->
        <!-- <template v-if="props.auth.user.role === 2">
          <v-btn
            v-if="!shiftStarted"
            color="yellow"
            prepend-icon="mdi-clock-start"
            size="small"
            class="text-white mr-2"
            @click="startShift"
          >
            Start Shift
          </v-btn>

          <v-btn
            v-else
            color="error"
            prepend-icon="mdi-clock-end"
            size="small"
            class="text-white"
            @click="endShift"
          >
            End Shift
          </v-btn>
        </template> -->
      </div>
    </v-app-bar>

    <!-- ✅ Enhanced Success Snackbar -->
    <v-snackbar
      v-model="showSuccess"
      color="success"
      timeout="4000"
      location="top"
      variant="elevated"
      elevation="8"
      class="custom-snackbar"
    >
      <div class="d-flex align-center">
        <v-icon start icon="mdi-check-circle" size="24" class="mr-3"></v-icon>
        <div>
          <div class="font-weight-bold text-body-1">Success!</div>
          <div class="text-body-2">{{ successMessage }}</div>
        </div>
      </div>
      <template v-slot:actions>
        <v-btn
          variant="text"
          icon="mdi-close"
          @click="showSuccess = false"
        ></v-btn>
      </template>
    </v-snackbar>

    <!-- Enhanced Error Dialog -->
    <v-dialog v-model="showErrorCard" max-width="500" persistent>
      <v-card rounded="lg" class="error-dialog">
        <div class="error-header pa-6 pb-4">
          <div class="d-flex align-center">
            <v-avatar color="error" size="56" class="mr-4">
              <v-icon size="32" color="white">mdi-alert-circle</v-icon>
            </v-avatar>
            <div>
              <h2 class="text-h6 font-weight-bold mb-1">Error Occurred</h2>
              <p class="text-caption text-medium-emphasis mb-0">
                Please review the message below
              </p>
            </div>
          </div>
        </div>
        
        <v-divider></v-divider>
        
        <v-card-text class="pa-6">
          <v-alert
            type="error"
            variant="tonal"
            density="comfortable"
            class="mb-0"
          >
            {{ errorCardMsg }}
          </v-alert>
        </v-card-text>
        
        <v-divider></v-divider>
        
        <v-card-actions class="pa-4 justify-end">
          <v-btn
            variant="flat"
            color="indigo-darken-4"
            size="large"
            @click="showErrorCard = false"
            class="px-6"
          >
            Got it
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Enhanced Navigation Drawer -->
    <v-navigation-drawer
      v-model="drawer"
      :location="$vuetify.display.mobile ? 'left' : undefined"
      temporary
      width="280"
      class="custom-drawer"
    >
      <!-- Drawer Header -->
      <div class="drawer-header pa-6 text-center">
        <v-avatar color="indigo-darken-4" size="80" class="mb-3">
          <v-icon size="48" color="white">mdi-parking</v-icon>
        </v-avatar>
        <h3 class="text-h6 font-weight-bold text-indigo-darken-4 mb-1">
          Carpark System
        </h3>
        <p class="text-caption text-medium-emphasis">
          Management Dashboard
        </p>
      </div>

      <v-divider></v-divider>

      <!-- Navigation Items -->
      <v-list class="navigation-list pa-2">
        <template v-for="item in items" :key="item.title">
          <v-list-group v-if="item.children" :value="item.value" class="nav-group">
            <template v-slot:activator="{ props: activatorProps }">
              <v-list-item
                v-bind="activatorProps"
                :title="item.title"
                @click="navigate(item)"
                color="indigo-darken-4"
                rounded="lg"
                class="mb-1"
              >
                <template v-slot:prepend>
                  <v-avatar color="indigo-lighten-5" size="40">
                    <v-icon :icon="item.icon" color="indigo-darken-4" size="20"></v-icon>
                  </v-avatar>
                </template>
              </v-list-item>
            </template>
            
            <v-list-item
              v-for="child in item.children"
              :key="child.title"
              :title="child.title"
              @click="navigate(child)"
              :active="child.value === activeItem"
              color="indigo-darken-4"
              rounded="lg"
              class="ml-4 mb-1 child-item"
            >
              <template v-slot:prepend>
                <v-icon :icon="child.icon" size="20"></v-icon>
              </template>
            </v-list-item>
          </v-list-group>

          <v-list-item
            v-else
            :title="item.title"
            @click="navigate(item)"
            :active="item.value === activeItem"
            color="indigo-darken-4"
            rounded="lg"
            class="mb-1 nav-item"
          >
            <template v-slot:prepend>
              <v-avatar color="indigo-lighten-5" size="40">
                <v-icon :icon="item.icon" color="indigo-darken-4" size="20"></v-icon>
              </v-avatar>
            </template>
          </v-list-item>
        </template>
      </v-list>

      <template v-slot:append>
        <div class="drawer-footer pa-4">
          <v-card color="indigo-lighten-5" rounded="lg" flat>
            <div class="pa-3 text-center">
              <v-icon size="32" color="indigo-darken-4" class="mb-2">
                mdi-information-outline
              </v-icon>
              <p class="text-caption text-indigo-darken-4 mb-0">
                Version 1.0.0
              </p>
            </div>
          </v-card>
        </div>
      </template>
    </v-navigation-drawer>

    <v-main class="main-content">
      <slot />
    </v-main>
  </v-layout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { route } from 'ziggy-js';
import { router, usePage } from '@inertiajs/vue3';

const drawer = ref(false);
const page = usePage();
const props = defineProps({ auth: Object });

// ✅ Shift state
// const shiftStarted = ref(false);

// const startShift = () => {
//   router.post(route('shiftlogs.start'), {}, {
//     onSuccess: (page) => {
//       if (page.props.flash?.error) {
//         showErrorCard.value = true;
//         errorCardMsg.value = page.props.flash.error;
//       } else {
//         shiftStarted.value = true;
//         showSuccess.value = true;
//         successMessage.value = page.props.flash?.success || 'Shift started successfully!';
//       }
//     },
//     onError: () => {
//       showErrorCard.value = true;
//       errorCardMsg.value = 'Failed to start shift.';
//     }
//   });
// };

// const endShift = () => {
//   router.post(route('shiftlogs.end'), {}, {
//     onSuccess: (page) => {
//       if (page.props.flash?.error) {
//         showErrorCard.value = true;
//         errorCardMsg.value = page.props.flash.error;
//       } else {
//         shiftStarted.value = false;
//         showSuccess.value = true;
//         successMessage.value = page.props.flash?.success || 'Shift ended successfully!';
//       }
//     },
//     onError: () => {
//       showErrorCard.value = true;
//       errorCardMsg.value = 'Failed to end shift.';
//     }
//   });
// };


const allItems = [
  { title: 'Dashboard', value: 'dashboard', route: 'dashboard', icon: 'mdi-view-dashboard' },
  ...(page.props.auth.user.role === 1
    ? [
        { title: 'Park In', value: 'park_in', route: 'parkin.index', icon: 'mdi-car-arrow-right' },
        { title: 'Park Out', value: 'park_out', route: 'parkout', icon: 'mdi-car-arrow-left' },
        { title: 'Card Template', route: 'card-template.index', value: 'card_template', icon: 'mdi-card-text-outline' },
      ]
    : []),

  { title: 'Card Inventory', route: 'card-inventory.index', value: 'card_inventory', icon: 'mdi-card-account-details' },
{ title: 'Logs', value: 'logs', route: 'logs', icon: 'mdi-history' },
  { title: 'Transactions', value: 'transactions', route: 'ticket.payments', icon: 'mdi-receipt-text' },

  ...(page.props.auth.user.role === 1
    ? [
        { title: 'Sell Card', route: 'sell-card.create', value: 'sell_card', icon: 'mdi-cash-register' },        
        { title: 'Users', value: 'users', route: 'users.index', icon: 'mdi-account-group' },
        { title: 'Settings', value: 'settings', route: 'company.index', icon: 'mdi-cog' },
      ]
    : []),


//   { title: 'Shift', value: 'shift', route: 'shifts.index', icon: 'mdi-cog' },
  { title: 'Log out', value: 'log_out', route: 'logout', icon: 'mdi-logout' },
];

const limitedItems = [
  { title: 'Dashboard', value: 'dashboard', route: 'dashboard', icon: 'mdi-view-dashboard' },
  { title: 'Park In', value: 'park_in', route: 'parkin.index', icon: 'mdi-car-arrow-right' },
  { title: 'Park Out', value: 'park_out', route: 'parkout', icon: 'mdi-car-arrow-left' },
  { title: 'Card Inventory', route: 'card-inventory.index', value: 'card_inventory', icon: 'mdi-card-account-details' },
  { title: 'Sell Card', route: 'sell-card.create', value: 'sell_card', icon: 'mdi-cash-register' },
  { title: 'Logs', value: 'logs', route: 'logs', icon: 'mdi-history' },
  { title: 'Log out', value: 'log_out', route: 'logout', icon: 'mdi-logout' },
];

const items = computed(() => {
  const role = props?.auth?.user?.role ;
  return role === 1 || role === 3 ? allItems : limitedItems;
});

const activeItem = computed(() => {
  const currentRouteName = page.props.ziggy.currentRouteName;
  for (const item of items.value) {
    if (item.route === currentRouteName) return item.value;
    if (item.children) {
      const foundChild = item.children.find(child => child.route === currentRouteName);
      if (foundChild) return foundChild.value;
    }
  }
  return 'home';
});

function navigate(item) {
  if (!item.route) return;
  if (item.route === 'logout') router.post(route('logout'));
  else router.visit(route(item.route));
  drawer.value = false;
}

const errorCardMsg = ref('');
const showErrorCard = ref(false);
const successMessage = ref('');
const showSuccess = ref(false);

const handlePopState = () => {
  sessionStorage.setItem('isBackNavigation', 'true');
};

onMounted(() => window.addEventListener('popstate', handlePopState));
onUnmounted(() => window.removeEventListener('popstate', handlePopState));

watch(() => page.props.flash.success, val => {
  if (sessionStorage.getItem('isBackNavigation') === 'true') {
    sessionStorage.removeItem('isBackNavigation');
    return;
  }
  if (val) {
    showSuccess.value = true;
    successMessage.value = val;
  }
}, { deep: true, immediate: true });

watch(() => page.props.errors, val => {
  if (val) {
    if (val.PLATENO) {
      showErrorCard.value = true;
      errorCardMsg.value = val.PLATENO;
    }
    if (val.qr_code) {
      showErrorCard.value = true;
      errorCardMsg.value = val.qr_code;
    }
    if (val.cash_amount) {
      showErrorCard.value = true;
      errorCardMsg.value = val.cash_amount;
    }
    if (val.cards) {
      showErrorCard.value = true;
      errorCardMsg.value = val.cards;
    }
  }
}, { immediate: true });

watch(() => page.props.flash?.error, val => {
  if (sessionStorage.getItem('isBackNavigation') === 'true') {
    sessionStorage.removeItem('isBackNavigation');
    return;
  }
  if (val) {
    showErrorCard.value = true;
    errorCardMsg.value = val;
  }
}, { deep: true, immediate: true });
</script>

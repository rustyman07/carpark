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

      <!-- User Info -->
      <div class="user-section mr-4" v-if="props.auth.user.name">
        <v-chip
          color="white"
          variant="flat"
          size="default"
          class="user-chip"
        >
          <v-avatar start color="indigo-lighten-2" size="32">
            <v-icon size="20">mdi-account</v-icon>
          </v-avatar>
          <span class="font-weight-medium">{{ props.auth.user.name }}</span>
        </v-chip>
      </div>
    </v-app-bar>

    <!-- Enhanced Success Snackbar -->
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
          <!-- Group Items -->
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
            
            <!-- Child Items -->
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

          <!-- Regular Items -->
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

      <!-- Drawer Footer -->
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

    <!-- Main Content -->
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
const props = defineProps({
  auth: Object,
});

const items = [
  { title: 'Dashboard', value: 'dashboard', route: 'dashboard', icon: 'mdi-view-dashboard' },
  { title: 'Park In', value: 'park_in', route: 'parkin.index', icon: 'mdi-car-arrow-right' },
  { title: 'Park Out', value: 'park_out', route: 'parkout', icon: 'mdi-car-arrow-left' },
  { title: 'Card Template', route: 'card-template.index', value: 'card_template', icon: 'mdi-card-text-outline' },
  { title: 'Card Inventory', route: 'card-inventory.index', value: 'card_inventory', icon: 'mdi-card-account-details' },
  { title: 'Sell Card', route: 'sell-card.create', value: 'sell_card', icon: 'mdi-cash-register' },
  { title: 'Logs', value: 'logs', route: 'logs', icon: 'mdi-history' },
  { title: 'Transactions', value: 'transactions', route: 'ticket.payments', icon: 'mdi-receipt-text' },
  { title: 'Users', value: 'users', route: 'users.index', icon: 'mdi-account-group' },
  {
    title: 'Settings',
    value: 'settings',
    route: 'company.index',
    icon: 'mdi-cog',
  },
  { title: 'Log out', value: 'log_out', route: 'logout', icon: 'mdi-logout' },
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
  if (!item.route) return;

  if (item.route === 'logout') {
    router.post(route('logout'));
  } else {
    router.visit(route(item.route));
  }
  
  drawer.value = false; // Close drawer after navigation
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
  window.addEventListener('popstate', handlePopState);
});

onUnmounted(() => {
  window.removeEventListener('popstate', handlePopState);
});

watch(
  () => page.props.flash.success,
  (val) => {
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
      if (val.cash_amount) {
        showErrorCard.value = true;
        errorCardMsg.value = val.cash_amount;
      }
      if (val.cards) {
        showErrorCard.value = true;
        errorCardMsg.value = val.cards;
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

<style scoped>
/* App Bar */
.app-bar-custom {
  box-shadow: 0 2px 12px rgba(26, 35, 126, 0.15) !important;
}

.toolbar-title {
  font-size: 1.25rem;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.nav-icon {
  transition: transform 0.3s ease;
}

.nav-icon:hover {
  transform: rotate(90deg);
}

.user-chip {
  transition: all 0.3s ease;
}

.user-chip:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

/* Snackbar */
.custom-snackbar {
  margin-top: 16px;
}

/* Error Dialog */
.error-dialog {
  overflow: hidden;
}

.error-header {
  background: linear-gradient(135deg, rgba(244, 67, 54, 0.05) 0%, rgba(244, 67, 54, 0.02) 100%);
}

/* Navigation Drawer */
.custom-drawer {
  border-right: 1px solid rgba(0, 0, 0, 0.08);
}

.drawer-header {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.05) 0%, rgba(26, 35, 126, 0.02) 100%);
}

/* Navigation List */
.navigation-list {
  overflow-y: auto;
  max-height: calc(100vh - 300px);
}

.navigation-list::-webkit-scrollbar {
  width: 6px;
}

.navigation-list::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.navigation-list::-webkit-scrollbar-thumb {
  background: #1a237e;
  border-radius: 10px;
}

.nav-item,
.child-item {
  transition: all 0.2s ease;
  margin-bottom: 4px;
}

.nav-item:hover,
.child-item:hover {
  transform: translateX(4px);
}

:deep(.v-list-item--active) {
  background: linear-gradient(135deg, #1a237e 0%, #283593 100%) !important;
  color: white !important;
}

:deep(.v-list-item--active .v-avatar) {
  background: rgba(255, 255, 255, 0.2) !important;
}

:deep(.v-list-item--active .v-icon) {
  color: white !important;
}

:deep(.v-list-item--active .v-list-item-title) {
  color: white !important;
  font-weight: 600;
}

.child-item {
  font-size: 0.9rem;
}

/* Drawer Footer */
.drawer-footer {
  border-top: 1px solid rgba(0, 0, 0, 0.08);
  background: white;
}

/* Main Content */
.main-content {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

/* Responsive */
@media (max-width: 600px) {
  .toolbar-title {
    font-size: 1rem;
  }

  .user-section {
    display: none;
  }

  .drawer-header {
    padding: 16px;
  }

  .drawer-header .v-avatar {
    width: 60px;
    height: 60px;
  }
}

/* Animations */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.nav-item,
.child-item {
  animation: slideIn 0.3s ease-out;
}
</style>
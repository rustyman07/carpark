<template>
  <v-card>
    <v-layout style="min-height: 100vh;">
      <v-app-bar color="blue-darken-4">
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title>Carpark</v-toolbar-title>
      </v-app-bar>

      <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'left' : undefined"
        temporary
      >
        <v-list>
          <template v-for="item in items" :key="item.title">
            <v-list-group v-if="item.children" :value="item.value">
              <template v-slot:activator="{ props }">
                <!-- <v-list-item
                  v-bind="props"
                  :title="item.title"
                  :active="isParentActive(item.value) || item.value === activeItem"
                  @click="navigate(item)"
                  color="blue-darken-4"
                > -->
                 <v-list-item
                  v-bind="props"
                  :title="item.title"
                  @click="navigate(item)"
                  color="blue-darken-4"
                >
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
import { ref, computed, onBeforeMount } from 'vue';
import { route } from 'ziggy-js';
import { router, usePage } from '@inertiajs/vue3';

const drawer = ref(false);
const page = usePage();

const items = [
  { title: 'Home', value: 'home', route:'home', icon: 'mdi-home' },
  { title: 'Park In', value: 'park_in', route:'parkin.index', icon: 'mdi-parking' },
  { title: 'Park Out', value: 'park_out', route:'parkout', icon: 'mdi-car-key' },
  { title: 'Logs', value: 'logs', route:'logs', icon: 'mdi-history' },
  { 
    title: 'Settings',
    value: 'settings',
    route: 'settings.index',
    icon: 'mdi-cog',
    children: [
      { title: 'Company', value: 'company_card_template', route: 'company.index', icon: 'mdi-credit-card-settings' },
      { title: 'Card Template', route: 'card-template.index', value: 'card_template', icon: 'mdi-list-box-outline' },
      { title: 'Card Inventory', route: 'card-inventory.index', value: 'card_inventory', icon: 'mdi-list-box-outline' }
    ]
  },
];


const activeItem = computed(() => {
    // 💡 Access the new property you just shared
    const currentRouteName = page.props.ziggy.currentRouteName;

    for (const item of items) {
        if (item.route === currentRouteName) {
            return item.value;
        }
        if (item.children) {
            const foundChild = item.children.find(child => child.route === currentRouteName);
            if (foundChild) {
                return foundChild.value;
            }
        }
    }
    return 'home';
});




// console.log('Current Route:', page.props.ziggy.route.name);

function navigate(item) {
  if (item.route) {
    router.visit(route(item.route));
    activeItem.value = item.value;
  }
}


const isParentActive = computed(() => (parentValue) => {
  const parentItem = items.find(item => item.value === parentValue);
  if (parentItem && parentItem.children) {
    return parentItem.children.some(child => child.value === activeItem.value);
  }
  return false;
});
</script>
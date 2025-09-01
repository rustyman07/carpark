<template>
  <v-card>
    <v-layout>
      <v-app-bar color="primary">
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title>My Files</v-toolbar-title>
      </v-app-bar>

      <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'bottom' : undefined"
        temporary
      >
        <v-list>
          <template v-for="item in items" :key="item.title">
            <v-list-group v-if="item.children" :value="item.value">
              <template v-slot:activator="{ props }">
                <v-list-item
                  v-bind="props"
                  :title="item.title"
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
            >
              <template v-slot:prepend>
                <v-icon :icon="item.icon"></v-icon>
              </template>
            </v-list-item>
          </template>
        </v-list>
      </v-navigation-drawer>

      <v-main style="height: 500px;">
        <v-card-text>
          The navigation drawer will appear from the bottom on smaller size screens.
        </v-card-text>
      </v-main>
    </v-layout>
  </v-card>
</template>

<script setup>
import { ref } from 'vue';

const drawer = ref(false);

const items = [
  { title: 'Home', value: 'home', icon: 'mdi-home' },
  { title: 'Park In', value: 'park_in', icon: 'mdi-directions-car' },
  { title: 'Park Out', value: 'park_out', icon: 'mdi-car-key' },
  { title: 'Logs', value: 'logs', icon: 'mdi-history' },
  {
    title: 'Settings',
    value: 'settings',
    icon: 'mdi-cog',
    children: [
      { title: 'Company Card Template', value: 'company_card_template', icon: 'mdi-credit-card-settings' },
      { title: 'Card Inventory', value: 'card_inventory', icon: 'mdi-list-box-outline' }
    ]
  },
];

function navigate(item) {
  console.log('Navigate to:', item.value);
}
</script>






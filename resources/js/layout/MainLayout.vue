<template>
  <v-card>
    <v-layout>
      <!-- App Bar -->
      <v-app-bar color="blue-darken-4">
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

        <v-toolbar-title>Carpark</v-toolbar-title>

        <template v-if="$vuetify.display.mdAndUp">
          <v-btn icon="mdi-magnify" variant="text"></v-btn>
          <v-btn icon="mdi-filter" variant="text"></v-btn>
        </template>

        <v-btn icon="mdi-dots-vertical" variant="text"></v-btn>
      </v-app-bar>

      <!-- Drawer -->
<v-navigation-drawer
  v-model="drawer"
  :location="$vuetify.display.mobile ? 'left' : undefined"
  temporary
>
  <v-list>
    <v-list-item
      v-for="item in items"
      :key="item.title"
    >
      <!-- Inertia Link -->
   <v-btn
  color="primary"
  variant="text"
>
  <Link :href="route(item.route)" class="text-none">
    {{ item.title }}
  </Link>
</v-btn>
      <!-- End Inertia Link -->
    </v-list-item>
  </v-list>
</v-navigation-drawer>

      <!-- Main Content -->
      <v-main style="height: 100vh;">
        
          <!-- ✅ Page content goes here -->
          <slot />
      
      </v-main>
    </v-layout>
  </v-card>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const items = [
  { title: 'Home', route: 'home' },
  { title: 'Park In', route: 'parkin.index' },
  { title: 'Park Out', route: 'parkout' },
  { title: 'Parking Logs', route: 'logs' },
//   { title: 'Ticket Logs', route: 'buzz' },
]

const drawer = ref(false)
const group = ref(null)

watch(group, () => {
  drawer.value = false
})
</script>

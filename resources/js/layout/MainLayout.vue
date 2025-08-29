<template>
  <v-card class="min-h-screen">
    <v-layout style="min-height: 100vh;" class="flex flex-col">

      <!-- App Bar -->
      <v-app-bar    color = "blue-darken-4" >
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title>Carpark</v-toolbar-title>

        <template v-if="$vuetify.display.mdAndUp">
          <v-btn icon="mdi-magnify" variant="text" class="text-white"></v-btn>
          <v-btn icon="mdi-filter" variant="text" class="text-white"></v-btn>
        </template>

        <v-btn icon="mdi-dots-vertical" variant="text" class="text-white"></v-btn>
      </v-app-bar>

      <!-- Drawer -->
      <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'left' : undefined"
        temporary
        app
        
      >
<div class="side-header-des h-25 bg-blue-darken-4 w-screen position-relative">
  <!-- Avatar overlaps the header -->
  <v-avatar
    class="avatar-float"
    size="128px"
   
  >
    <v-img
      alt="Avatar"
      src="https://via.placeholder.com/150"
    />
  </v-avatar>
</div>

<v-layout
  class="d-flex flex-column   ga-4 mt-16 "
>
  <v-list class="d-flex flex-column justify-center wfull">
    <v-list-item v-for="item in items" :key="item.title">
      <Link
        :href="route(item.route)"
        class="sidebar-link px-2 py-2 rounded  d-flex align-center ga-8 wfull"
      >
        <span style="font-size: 32px;" class="text-blue-darken-4 material-symbols-outlined">
          {{ item.icon }}
        </span>
        <span style="font-size: 18px;" class="text-blue-darken-4">
          {{ item.title }}
        </span>
      </Link>
    </v-list-item>
  </v-list>
</v-layout>

      </v-navigation-drawer>

      <!-- Main Content -->
      <v-main class="flex-1 bg-grey-lighten-5">
        <slot />
      </v-main>

    </v-layout>
  </v-card>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import 'material-symbols'

const items = [
  { title: 'Home', route: 'home', icon: 'home' },
  { title: 'Park In', route: 'parkin.index', icon: 'directions_car' },
  { title: 'Park Out', route: 'parkout', icon: 'car_tag'},
  { title: 'Parking Logs', route: 'logs', icon: 'overview' },
   { title: 'Card Template', route: 'card-template.index', icon: 'credit_card_gear' },
    { title: 'Card Inventory', route: 'card-inventory.index', icon: 'list_alt' },
]

const drawer = ref(false)
</script>

<style scoped>
.sidebar-link {
  text-decoration: none;       /* remove underline */
  font-family: 'Roboto', sans-serif; /* or any font */
  font-size: 16px;             /* adjust size */
  font-weight: 500;            /* medium bold */

}
.sidebar-link:hover,
.sidebar-link:focus {
  background-color: #e3f2fd; /* Vuetify blue-lighten-5 */
  outline: none; /* optional: remove default outline */
}

.side-header-des {
  border-radius: 0 0 5px 5px; /* top-left, top-right, bottom-right, bottom-left */
}

.avatar-float {
  position: absolute;
  bottom: 0;             /* stick to bottom of header */
  left: 50%;             /* center horizontally */
  transform: translate(-50%, 50%); 
  /* -50% X centers, +50% Y pushes it down half its height */
  z-index: 10;
}

</style>

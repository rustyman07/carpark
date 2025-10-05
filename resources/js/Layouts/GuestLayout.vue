<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
<div
  class="relative bg-center bg-cover flex flex-col min-h-screen items-center pt-6 sm:justify-center sm:pt-0"
>
  <!-- ✅ White overlay -->
  <!-- <div class="absolute inset-0 bg-white/90"></div> -->

  <!-- ✅ Your content (kept above overlay) -->
 
    <slot />
 
</div>

</template>

<!-- <style> 

.background-container::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background-color: rgba(0, 0, 0, 0.1); /* Semi-transparent black */
  z-index: -1; /* Pushes the overlay behind the content but in front of the background image */
}
</style> -->
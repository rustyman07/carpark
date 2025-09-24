<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="background-container bg-gray-100/30 bg-[url(/images/carpark.png)]  bg-blend-lighten flex min-h-screen flex-col items-center  pt-6 sm:justify-center sm:pt-0"
    >
        <!-- <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div> -->

        <div
            class="mt-6  bg-w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
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
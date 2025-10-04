<template>
        <div>
          <v-text-field
            class="text-h1"
            v-model="form.PLATENO"
            placeholder="Enter Plate No."
            style="width: 300px"
            variant="underlined"
          />
          <!-- <v-btn     :disabled="!form.PLATENO" size="x-large" @click="submitPlate" block color="indigo-darken-4">
            Submit QR
          </v-btn> -->
          <Link
        :href="route(item.route)">
    
        Submit QR
    </Link>
      </div>

</template>

<script setup>

import { useForm, usePage, } from '@inertiajs/vue3';
import { computed,onMounted ,ref,watch} from 'vue';
import dayjs from 'dayjs';
import { Link,router } from '@inertiajs/vue3'


const page = usePage();

const form = useForm ({
    QRCODE : '',
})

</script>
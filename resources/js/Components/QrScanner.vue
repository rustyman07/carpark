<template>
      <v-dialog v-model="isScanQRS" max-width="600">
    <v-card>
      <v-card-title>Scan QR Code</v-card-title>
      <v-card-text>
        <div id="reader" style="width:100%; height:400px;"></div>
      </v-card-text>
      <v-card-actions>
        <v-btn color="error" text @click="closeScanner">Close</v-btn>
      </v-card-actions>
    </v-card>
    </v-dialog>
</template>
<script setup>
const props = defineProps({
    closeScanner : Function
})

const  isScanQRS = defineModel({ type: Boolean, default: false })


</script>
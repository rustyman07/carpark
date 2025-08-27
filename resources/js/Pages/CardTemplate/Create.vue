<!-- Create.vue -->
<template>
  <v-dialog v-model="dialog" max-width="600">
    <form @submit.prevent = "isEdit? update : create">
      <v-card title="TEMPLATE">
      <v-card-text>
        <!-- your form fields -->
        
        <v-text-field  v-model= form.CARDNAME variant="underlined" label="Card Name" type = 'text' :error-messages="form.errors.CARDNAME" />
         
        <div class="d-flex ga-2">
       
            <v-text-field  v-model= form.NOOFDAYS variant="underlined" label="No of Days" type="number" :error-messages="form.errors.NOOFDAYS"/>     
            <v-text-field  v-model= form.PRICE variant="underlined" label="Price"  type="number" :error-messages="form.errors.PRICE" />     
        </div>
        <div class="d-flex ga-2">     
            <v-text-field  v-model= form.DISCOUNT variant="underlined" label="Discount"  type="number" :error-messages="form.errors.DISCOUNT" />
            <v-text-field  v-model= form.AMOUNT variant="underlined" label="Amount"  type="number" :error-messages="form.errors.AMOUNT" />          
        </div>
        
        <!-- ...rest of form -->
      </v-card-text>

      <v-divider />

      <v-card-actions>
        <v-spacer />
        <v-btn text="Close" variant="plain" @click="dialog = false" />
        <v-btn color="primary" :text="isEdit?  'UPDATE':'SAVE'" variant="tonal" type="submit" />
      </v-card-actions>
    </v-card>
    </form>
  
  </v-dialog>
</template>

<script setup>
import { shallowRef,watch ,ref} from 'vue'
import { useForm, usePage, } from '@inertiajs/vue3';

// 🔹 This controls dialog open/close internally
const dialog = defineModel({ type: Boolean, default: false });

const props = defineProps({
  modelValue: { type: Boolean, required: true }, // dialog
  selectedTemplate: { type: Object, default: null }      // editing item
});

const isEdit = ref(false);

const form = useForm({
    CARDNAME : '',
    NOOFDAYS : 0,
    PRICE: 0,
    DISCOUNT: null,
    AMOUNT: 0,
})
console.log(isEdit.value);

watch(() => props.selectedTemplate,(val) => {

  if (val) {
    isEdit.value = true;
    console.log(isEdit.value);
    form.CARDNAME = val.CARDNAME
    form.NOOFDAYS = val.NOOFDAYS
    form.PRICE = val.PRICE
    form.DISCOUNT = val.DISCOUNT
    form.AMOUNT = val.AMOUNT
  } else {
     isEdit.value = false;
     console.log(isEdit.value);
    form.reset() // add mode → clear form
  }
})

const create = () => form.post(route('cardTemplate.store'),{
    onSuccess: ()=>{
        form.reset();
        dialog.value = false
    },
    onError: ()=>{

    }
});


const update = () => form.post(route('cardTemplate.update'),{
    onSuccess: ()=>{
        form.reset();
        dialog.value = false
    },
    onError: ()=>{

    }
});




</script>

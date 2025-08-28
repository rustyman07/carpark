<!-- Create.vue -->
<template>
  <v-dialog v-model="dialog" max-width="600">
    <form @submit.prevent = "isEdit? update() : create()">
      <v-card title="TEMPLATE">
      <v-card-text>
        <!-- your form fields -->
         <v-select
  label="Select"
  :items="props.cardTemplate"
  item-title="CARDNAME"   
  item-value="id"         
  v-model="form.CARD_TEMPLATEID"
/>

  
        <v-text-field  v-model= form.CARDNAME variant="underlined" label="Card Name" type = 'text' :error-messages="form.errors.CARDNAME" />
        
       
            <v-text-field  v-model= form.NOOFDAYS variant="underlined" label="No of Days" type="number" :error-messages="form.errors.NOOFDAYS"/>     
            <v-text-field  v-model= form.PRICE variant="underlined" label="Price"  type="number" :error-messages="form.errors.PRICE" />     
      
        
            <v-text-field  v-model= form.DISCOUNT variant="underlined" label="Discount"  type="number" :error-messages="form.errors.DISCOUNT" />
                   
        
        
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
  modelValue: { type: Boolean, required: true }, 
   cardTemplate: { type: Array, default: () => [] }      
});

const isEdit = ref(false);

const form = useForm({
    CARD_TEMPLATEID : null,
    CARDNAME : '',
    NOOFDAYS : 1,
    PRICE: 0,
    DISCOUNT: null,
   
})
console.log(isEdit.value);
console.log(props.template)

// watch(() => props.selectedTemplate,(val) => {

//   if (val) {
//     isEdit.value = true;
//     console.log(isEdit.value);
//     form.CARDNAME = val.CARDNAME
//     form.NOOFDAYS = val.NOOFDAYS
//     form.PRICE = val.PRICE
//     form.DISCOUNT = val.DISCOUNT

//   } else {
//      isEdit.value = false;
//      console.log(isEdit.value);
//     form.reset() 
//   }
// })

// const create = () => form.post(route('card-template.store'),{
//     onSuccess: ()=>{
//         form.reset();
//         dialog.value = false
//     },
//     onError: (errors)=>{
// console.log(errors);
//     }
//   });


// const update = () => form.put(route('card-template.update', props.selectedTemplate.id), {
//   onSuccess: () => {
//     form.reset()
//     dialog.value = false
//   },
//     onError: (errors)=>{
// console.log(errors);
//     }
// })




</script>

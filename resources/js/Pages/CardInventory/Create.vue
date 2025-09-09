<!-- Create.vue -->
<template>
  <v-dialog v-model="dialog" max-width="600">
    <form @submit.prevent = "isEdit? update() : create()">
      <v-card title="TEMPLATE">
      <v-card-text>
        <!-- your form fields -->
<v-select
  v-model="form.card_template_id"
  :items="props.cardTemplate"
  item-title="card_name"
  item-value="id"
  @update:modelValue="onTemplateChange"
/>

        <v-text-field  v-model= form.card_name variant="underlined" label="Card Name" type = 'text' :error-messages="form.errors.card_name" />
        <v-text-field  v-model= form.no_of_cards variant="underlined" label="No. of Cards"  type="number" :error-messages="form.errors.no_of_cards" />
        <v-text-field  v-model= form.no_of_days variant="underlined" label="No. of Days" type="number" :error-messages="form.errors.no_of_days"/>     
        <v-text-field  v-model= form.price variant="underlined" label="price"  type="number" :error-messages="form.errors.price" />   
              
        <v-text-field  v-model= form.discount variant="underlined" label="Discount"  type="number" :error-messages="form.errors.discount" />
                   
        
        
        <!-- ...rest of form -->
      </v-card-text>

      <v-divider />

      <v-card-actions>
        <v-spacer />
        <v-btn text="Close" variant="plain" @click="dialog = false" />
        <v-btn color="primary" :text="isEdit?  'UPDATE':'SAVE'" variant="tonal" type="submit"  :disabled="form.processing"/>
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

   cardTemplate: { type: Array, default: () => [] }      
});

const isEdit = ref(false);

const form = useForm({
     no_of_cards:1,
    card_template_id : null,
    card_name : '',
    no_of_days : 1,
    price: 0,
    discount: null,
   
})
console.log(isEdit.value);
console.log(props.template)

// watch(() => props.selectedTemplate,(val) => {

//   if (val) {
//     isEdit.value = true;
//     console.log(isEdit.value);
//     form.card_name = val.card_name
//     form.NOOFDAYS = val.NOOFDAYS
//     form.price = val.price
//     form.discount = val.discount

//   } else {
//      isEdit.value = false;
//      console.log(isEdit.value);
//     form.reset() 
//   }
// })

const create = () => form.post(route('card-inventory.store'),{
    onSuccess: ()=>{
        form.reset();
        dialog.value = false
    },
    onError: (errors)=>{
console.log(errors);
    }
  });


const update = () => form.put(route('card-inventory.update', props.selectedTemplate.id), {
  onSuccess: () => {
    form.reset()
    dialog.value = false
  },
    onError: (errors)=>{
console.log(errors);
    }
})


const onTemplateChange = (id) => {
  console.log('testtgsd');
  const selected = props.cardTemplate.find(t => t.id === id)
  if (selected) {
    form.card_name   = selected.card_name
    form.no_of_days = selected.no_of_days
    form.price      = selected.price
    form.discount   = selected.discount
  }
}



</script>

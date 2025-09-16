<template>
     <div class="d-flex justify-center   pa-10">
    <v-card class="pa-4  " min-width="400">
        <v-card-title class="text">Company Settings</v-card-title>
        <v-divider></v-divider>

    <v-text-field  class="mt-4"
    label="Company Name"
        variant="solo"
    input-class="text-caption"
    v-model="form.name"
 
    ></v-text-field>

    <v-text-field 
    label="Address"
        variant="solo"
    v-model="form.address"
    ></v-text-field>


    <v-text-field 
    
    label="Contact"
    variant="solo"
    v-model="form.contact"
    ></v-text-field>

    <!-- <v-text-field 
    label="Contact"
        variant="solo"
    v-model="form.post_paid_rate"
    ></v-text-field> -->

          <v-select
        label="Rate type"
         variant="solo"
        :items="types"
        item-title="label"
        item-value="value"
        v-model="form.rate"
      />
<v-row>
        <v-col>
    <v-text-field 

    :disabled=" isDisabled && form.rate !== 'perhour'"
    label="Rate per hour"
        variant="solo"
    v-model="form.rate_perhour"
    ></v-text-field>
    </v-col>
    <v-col>

    <v-text-field 
    label="Rate per day"
    :disabled=" isDisabled ? form.rate == 'perday' : false"
        variant="solo"
    v-model="form.rate_perday"
    ></v-text-field>
    </v-col>


</v-row>


    <v-card-text>
   <!-- <div>    Company Name:   <span>{{ company.name }}</span></div>
   <div>Address:   <span>{{ company.address }}</span></div>
   <div>Contact:   <span>{{ company.contact }}</span></div>
   <div>po:   <span>{{ company.post_paid_rate }}</span></div> -->
    </v-card-text>

    <v-layout>
        <v-btn  @click="updateCompany" color="blue-darken-4"> update</v-btn>
    </v-layout>  
    </v-card>
  </div>
   
</template>
<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { id } from 'vuetify/locale';


const page = usePage();

const company = page.props.company;

const isDisabled = ref(true);
    
const form = useForm({
    id : company.id,
    name : company.name,
    address : company.address,
    contact : company.contact,
    post_paid_rate : company.post_paid_rate,
    rate : company.rate,
    rate_perhour : company.rate_perhour,
    rate_perday : company.rate_perday,

});


const types = [
  { label: 'Per hour', value: 'perhour' },
  { label: 'Per day', value: 'perday' },
  { label: 'Combination', value: 'combination' },
];
const selectedType = ref(company.rate || 'perhour');

const updateCompany = () =>{
    isDisabled.value = !isDisabled.value;

}





</script>
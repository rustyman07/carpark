<template>
     <div class="d-flex justify-center">
    <v-card class="pa-4  " min-width="400">
        <v-card-title>Company Settings</v-card-title>
        <v-divider></v-divider>

    <v-text-field class="mt-4"
    label="Company Name"
    variant="filled"
    v-model="form.name"
    
    ></v-text-field>

    <v-text-field class="mt-4"
    label="Address"
    variant="filled"
    v-model="form.address"
    ></v-text-field>


    <v-text-field class="mt-4"
    label="Contact"
    variant="filled"
    v-model="form.contact"
    ></v-text-field>

    <v-text-field class="mt-4"
    label="Contact"
    variant="filled"
    v-model="form.post_paid_rate"
    ></v-text-field>



    <v-card-text>
   <!-- <div>    Company Name:   <span>{{ company.name }}</span></div>
   <div>Address:   <span>{{ company.address }}</span></div>
   <div>Contact:   <span>{{ company.contact }}</span></div>
   <div>po:   <span>{{ company.post_paid_rate }}</span></div> -->
    </v-card-text>

    <v-layout>
        <v-btn color="blue-darken-4"> update</v-btn>
    </v-layout>  
    </v-card>
  </div>
   
</template>
<script setup>
import { useForm, usePage } from '@inertiajs/vue3';


const page = usePage();

const company = page.props.company;

const form = useForm({
    name : company.name,
    address : company.address,
    contact : company.contact,
    post_paid_rate : company.post_paid_rate

});




</script>
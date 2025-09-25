<template>
  <div class="d-flex justify-center pa-10">
    <v-card class="pa-4" min-width="400">
      <div class="text-h6 font-semibold mb-2">Company Settings</div>
      <v-divider></v-divider>

      <!-- Company Name -->
      <v-text-field
        class="mt-4"
        label="Company Name"
        variant="solo"
        input-class="text-caption"
        v-model="form.name"
        :disabled="isDisabled"
      />

      <!-- Address -->
      <v-text-field
        label="Address"
        variant="solo"
        v-model="form.address"
        :disabled="isDisabled"
      />

      <!-- Contact -->
      <v-text-field
        label="Contact"
        variant="solo"
        v-model="form.contact"
        :disabled="isDisabled"
      />

      <!-- Rate type -->
      <v-select
        label="Rate type"
        variant="solo"
        :items="types"
        item-title="label"
        item-value="value"
        v-model="form.rate"
        :disabled="isDisabled"
      />

      <v-row>
        <v-col>
          <!-- Rate per hour -->
          <v-text-field
            label="Rate per hour"
            variant="solo"
            v-model="form.rate_perhour"
            :disabled="isDisabled || (form.rate !== 'perhour' && form.rate !== 'combination')"
          />
        </v-col>

        <v-col>
          <!-- Rate per day -->
          <v-text-field
            label="Rate per day"
            variant="solo"
            v-model="form.rate_perday"
            :disabled="isDisabled || (form.rate !== 'perday' && form.rate !== 'combination')"
          />
        </v-col>
      </v-row>

      <v-layout class="mt-4">
        <!-- Toggle Edit / Update Button -->
        <v-btn v-if="isDisabled" @click="isDisabled = false" color="blue-darken-4">
          Edit
        </v-btn>
        <v-btn v-else @click="updateCompany" color="green-darken-4">
          Update
        </v-btn>
      </v-layout>
    </v-card>
  </div>
</template>

<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const page = usePage();
const company = page.props.company;

// Controls whether inputs are editable
const isDisabled = ref(true);

// Initialize form with company data
const form = useForm({
  id: company.id,
  name: company.name,
  address: company.address,
  contact: company.contact,
  post_paid_rate: company.post_paid_rate,
  rate: company.rate,
  rate_perhour: company.rate_perhour,
  rate_perday: company.rate_perday,
});

// Options for rate types
const types = [
  { label: "Per hour", value: "perhour" },
  { label: "Per day", value: "perday" },
  { label: "Combination", value: "combination" },
];

// Submit update request
const updateCompany = () => {
  form.put(route("company.update", form.id), {
    preserveScroll: true,
    onSuccess: () => {
      isDisabled.value = true; // Lock fields again
      console.log("✅ Company updated successfully!");
    },
    onError: (errors) => {
      console.error("❌ Validation failed:", errors);
    },
  });
};
</script>

<style scoped>
.text-h6 {
  font-size: 1.25rem;
}
</style>

<template>
  <div class="settings-wrapper">
    <v-container class="py-8">
      <v-row justify="center">
        <v-col cols="12" md="10" lg="8">
          <v-card class="settings-card elevation-1" rounded="lg">
            <!-- Header Section -->
            <div class="card-header pa-6">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <h2 class="text-h5 text-indigo-darken-4 font-weight-bold mb-1">Company Settings</h2>
                  <p class="text-body-2 text-medium-emphasis mb-0">
                    Manage your company information and billing preferences
                  </p>
                </div>
                <v-icon size="40" color="indigo-darken-4" class="settings-icon">
                  mdi-office-building-cog
                </v-icon>
              </div>
            </div>

            <v-divider></v-divider>

            <!-- Form Section -->
            <div class="pa-6">
              <!-- Basic Information -->
              <div class="section-title mb-4">
                <v-icon class="mr-2" color="indigo-darken-4">mdi-information</v-icon>
                <span class="text-subtitle-1 font-weight-semibold">Basic Information</span>
              </div>

              <v-row>
                <v-col cols="12">
                  <v-text-field
                    label="Company Name"
                    variant="outlined"
                    density="comfortable"
                    v-model="form.name"
                    :disabled="isDisabled"
                    prepend-inner-icon="mdi-domain"
                    :readonly="isDisabled"
                    bg-color="surface"
                  />
                </v-col>

                <v-col cols="12">
                  <v-text-field
                    label="Address"
                    variant="outlined"
                    density="comfortable"
                    v-model="form.address"
                    :disabled="isDisabled"
                    prepend-inner-icon="mdi-map-marker"
                    :readonly="isDisabled"
                    bg-color="surface"
                  />
                </v-col>

                <v-col cols="12">
                  <v-text-field
                    label="Contact Number"
                    variant="outlined"
                    density="comfortable"
                    v-model="form.contact"
                    :disabled="isDisabled"
                    prepend-inner-icon="mdi-phone"
                    :readonly="isDisabled"
                    bg-color="surface"
                  />
                </v-col>
              </v-row>

              <!-- Billing Configuration -->
              <div class="section-title mb-4 mt-6">
                <v-icon class="mr-2" color="indigo-darken-4">mdi-cash-multiple</v-icon>
                <span class="text-subtitle-1 font-weight-semibold">Billing Configuration</span>
              </div>

              <v-row>
                <v-col cols="12">
                  <v-select
                    label="Rate Type"
                    variant="outlined"
                    density="comfortable"
                    :items="types"
                    item-title="label"
                    item-value="value"
                    v-model="form.rate"
                    :disabled="isDisabled"
                    prepend-inner-icon="mdi-clipboard-text-clock"
                    :readonly="isDisabled"
                    bg-color="surface"
                  >
                    <template v-slot:item="{ props, item }">
                      <v-list-item v-bind="props">
                        <template v-slot:prepend>
                          <v-icon :icon="getRateIcon(item.raw.value)"></v-icon>
                        </template>
                      </v-list-item>
                    </template>
                  </v-select>
                </v-col>

                <v-col cols="12" sm="6">
                  <v-text-field
                    label="Rate per Hour"
                    variant="outlined"
                    density="comfortable"
                    v-model="form.rate_perhour"
                    :disabled="isDisabled || (form.rate !== 'perhour' && form.rate !== 'combination')"
                    type="number"
                    :readonly="isDisabled || (form.rate !== 'perhour' && form.rate !== 'combination')"
                    bg-color="surface"
                  />
                </v-col>

                <v-col cols="12" sm="6">
                  <v-text-field
                    label="Rate per Day"
                    variant="outlined"
                    density="comfortable"
                    v-model="form.rate_perday"
                    :disabled="isDisabled || (form.rate !== 'perday' && form.rate !== 'combination')"
              
                    type="number"
                    :readonly="isDisabled || (form.rate !== 'perday' && form.rate !== 'combination')"
                    bg-color="surface"
                  />
                </v-col>
              </v-row>

              <!-- Combination Rate Settings -->
              <v-expand-transition>
                <div v-if="form.rate === 'combination'">
                  <v-alert
                    type="info"
                    variant="tonal"
                    class="mb-4"
                    density="compact"
                    icon="mdi-information-outline"
                  >
                    Configure how hourly and daily rates combine
                  </v-alert>

                  <v-row>
                    <v-col cols="12" >
                      <v-text-field
                        label="Hourly Billing Limit"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.hourly_billing_limit"
                        :disabled="isDisabled"
                        type="number"
                        min="1"
                        suffix="hours"
                        prepend-inner-icon="mdi-timer-sand"
                        hint="Hours charged before switching to daily rate"
                        persistent-hint
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                  </v-row>
                </div>
              </v-expand-transition>

              <!-- Grace Period Display -->
              <v-expand-transition>
                <div v-if="form.rate === 'perday' || form.rate === 'combination'">
                  <v-row>
                    <v-col cols="12" >
                      <v-text-field
                        label="Grace Period"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.grace_minutes"
                        :disabled="isDisabled"
                        type="number"
                        min="0"
                        suffix="minutes"
                        prepend-inner-icon="mdi-clock-plus-outline"
                        hint="Free minutes allowed beyond hourly or daily limit"
                        persistent-hint
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                  </v-row>
                </div>
              </v-expand-transition>
            </div>

            <v-divider></v-divider>

            <!-- Action Buttons -->
            <div class="pa-6">
              <div class="d-flex justify-end ga-3">
                <v-btn
                  v-if="!isDisabled"
                  variant="outlined"
                  @click="cancelEdit"
                  prepend-icon="mdi-close"
                  size="large"
                  color="indigo-darken-4"
                >
                  Cancel
                </v-btn>
                
                <v-btn
                  v-if="isDisabled"
                  color="primary"
                  @click="isDisabled = false"
                  prepend-icon="mdi-pencil"
                  size="large"
                  class="px-6"
                >
                  Edit Settings
                </v-btn>
                
                <v-btn
                  v-else
                  color="indigo-darken-4"        
                  @click="updateCompany"
                  prepend-icon="mdi-content-save"
                  size="large"
                  class="px-6"
                  :loading="form.processing"
                >
                  Save Changes
                </v-btn>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const page = usePage();
const company = page.props.company;

// Controls whether inputs are editable
const isDisabled = ref(true);

// Store original values for cancel functionality
const originalValues = {
  name: company.name,
  address: company.address,
  contact: company.contact,
  rate: company.rate,
  rate_perhour: company.rate_perhour,
  rate_perday: company.rate_perday,
  hourly_billing_limit: company.hourly_billing_limit,
  grace_minutes: company.grace_minutes
};

const form = useForm({
  id: company.id,
  name: company.name,
  address: company.address,
  contact: company.contact,
  rate: company.rate,
  rate_perhour: company.rate_perhour,
  rate_perday: company.rate_perday,
  hourly_billing_limit: company.hourly_billing_limit,
  grace_minutes: company.grace_minutes
});

const types = [
  { label: "Per Hour", value: "perhour" },
  { label: "Per Day", value: "perday" },
  { label: "Combination", value: "combination" },
];

// Get icon based on rate type
const getRateIcon = (value) => {
  switch(value) {
    case 'perhour': return 'mdi-clock-outline';
    case 'perday': return 'mdi-calendar-today';
    case 'combination': return 'mdi-alarm-multiple';
    default: return 'mdi-cash';
  }
};

// Cancel edit and restore original values
const cancelEdit = () => {
  Object.keys(originalValues).forEach(key => {
    form[key] = originalValues[key];
  });
  isDisabled.value = true;
};

// Submit update request
const updateCompany = () => {
  form.put(route("company.update", form.id), {
    preserveScroll: true,
    onSuccess: () => {
      isDisabled.value = true;
      Object.keys(originalValues).forEach(key => {
        originalValues[key] = form[key];
      });
      console.log("✅ Company updated successfully!");
    },
    onError: (errors) => {
      console.error("❌ Validation failed:", errors);
    },
  });
};
</script>

<style scoped>
.settings-wrapper {
  min-height: 100vh;
}

.settings-card {
  overflow: hidden;
  transition: transform 0.2s ease;
}

.card-header {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}

.settings-icon {
  opacity: 0.8;
}

.section-title {
  display: flex;
  align-items: center;
  padding-bottom: 8px;
  border-bottom: 2px solid rgba(102, 126, 234, 0.2);
}

:deep(.v-field--focused) {
  box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
}

:deep(.v-field), :deep(.v-btn) {
  transition: all 0.2s ease;
}

:deep(.v-field--disabled) {
  opacity: 0.7;
}

.v-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>

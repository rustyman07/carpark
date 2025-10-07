<template>
  <div class="shift-settings-wrapper">
    <v-container class="py-8">
      <v-row justify="center">
        <v-col cols="12" md="10" lg="8">
          <v-card class="settings-card elevation-1" rounded="lg">
            <!-- Header Section -->
            <div class="card-header pa-6">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <h2 class="text-h5 text-indigo-darken-4 font-weight-bold mb-1">Shift Management</h2>
                  <p class="text-body-2 text-medium-emphasis mb-0">
                    Configure staff shifts and working hours
                  </p>
                </div>
                <v-icon size="40" color="indigo-darken-4" class="settings-icon">
                  mdi-clock-outline
                </v-icon>
              </div>
            </div>

            <v-divider></v-divider>

            <!-- Form Section -->
            <div class="pa-6">
              <!-- Shift Configuration -->
              <div class="section-title mb-4">
                <v-icon class="mr-2" color="indigo-darken-4">mdi-cog</v-icon>
                <span class="text-subtitle-1 font-weight-semibold">Shift Configuration</span>
              </div>

              <v-alert
                type="info"
                variant="tonal"
                class="mb-4"
                density="compact"
                icon="mdi-information-outline"
              >
                Configure up to 3 shifts for your staff. Each shift will be available for staff assignment.
              </v-alert>

              <!-- Shift 1 -->
              <v-card variant="outlined" class="mb-4">
                <v-card-title class="bg-blue-lighten-5">
                  <v-icon color="blue" class="mr-2">mdi-clock</v-icon>
                  Shift 1
                </v-card-title>
                <v-card-text class="pt-4">
                  <v-row>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        label="Shift Name"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.shift1_name"
                        :disabled="isDisabled"
                        prepend-inner-icon="mdi-form-textbox"
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                    <v-col cols="12" sm="3">
                      <v-text-field
                        label="Start Time"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.shift1_start"
                        :disabled="isDisabled"
                        type="time"
                        prepend-inner-icon="mdi-clock-start"
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                    <v-col cols="12" sm="3">
                      <v-text-field
                        label="End Time"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.shift1_end"
                        :disabled="isDisabled"
                        type="time"
                        prepend-inner-icon="mdi-clock-end"
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>

              <!-- Shift 2 -->
              <v-card variant="outlined" class="mb-4">
                <v-card-title class="bg-green-lighten-5">
                  <v-icon color="green" class="mr-2">mdi-clock</v-icon>
                  Shift 2
                </v-card-title>
                <v-card-text class="pt-4">
                  <v-row>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        label="Shift Name"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.shift2_name"
                        :disabled="isDisabled"
                        prepend-inner-icon="mdi-form-textbox"
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                    <v-col cols="12" sm="3">
                      <v-text-field
                        label="Start Time"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.shift2_start"
                        :disabled="isDisabled"
                        type="time"
                        prepend-inner-icon="mdi-clock-start"
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                    <v-col cols="12" sm="3">
                      <v-text-field
                        label="End Time"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.shift2_end"
                        :disabled="isDisabled"
                        type="time"
                        prepend-inner-icon="mdi-clock-end"
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>

              <!-- Shift 3 -->
              <v-card variant="outlined" class="mb-4">
                <v-card-title class="bg-orange-lighten-5">
                  <v-icon color="orange" class="mr-2">mdi-clock</v-icon>
                  Shift 3
                </v-card-title>
                <v-card-text class="pt-4">
                  <v-row>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        label="Shift Name"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.shift3_name"
                        :disabled="isDisabled"
                        prepend-inner-icon="mdi-form-textbox"
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                    <v-col cols="12" sm="3">
                      <v-text-field
                        label="Start Time"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.shift3_start"
                        :disabled="isDisabled"
                        type="time"
                        prepend-inner-icon="mdi-clock-start"
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                    <v-col cols="12" sm="3">
                      <v-text-field
                        label="End Time"
                        variant="outlined"
                        density="comfortable"
                        v-model="form.shift3_end"
                        :disabled="isDisabled"
                        type="time"
                        prepend-inner-icon="mdi-clock-end"
                        :readonly="isDisabled"
                        bg-color="surface"
                      />
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>

              <!-- Additional Settings -->
              <div class="section-title mb-4 mt-6">
                <v-icon class="mr-2" color="indigo-darken-4">mdi-account-cog</v-icon>
                <span class="text-subtitle-1 font-weight-semibold">Staff Assignment</span>
              </div>

              <v-row>
                <v-col cols="12" sm="6">
                  <v-select
                    label="Default Shift for New Staff"
                    variant="outlined"
                    density="comfortable"
                    :items="shiftOptions"
                    v-model="form.default_shift"
                    :disabled="isDisabled"
                    prepend-inner-icon="mdi-account-plus"
                    :readonly="isDisabled"
                    bg-color="surface"
                  />
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    label="Shift Change Notice (days)"
                    variant="outlined"
                    density="comfortable"
                    v-model="form.shift_change_notice"
                    :disabled="isDisabled"
                    type="number"
                    min="1"
                    max="30"
                    suffix="days"
                    prepend-inner-icon="mdi-calendar-alert"
                    hint="Minimum notice required for shift changes"
                    persistent-hint
                    :readonly="isDisabled"
                    bg-color="surface"
                  />
                </v-col>
              </v-row>

              <!-- Current Shift Preview -->
              <v-expand-transition>
                <v-card v-if="!isDisabled" variant="tonal" class="mt-4">
                  <v-card-title class="text-subtitle-1 font-weight-medium">
                    <v-icon class="mr-2">mdi-eye</v-icon>
                    Shift Preview
                  </v-card-title>
                  <v-card-text>
                    <v-list density="compact">
                      <v-list-item v-if="form.shift1_name">
                        <template v-slot:prepend>
                          <v-avatar color="blue-lighten-5" size="32">
                            <v-icon color="blue">mdi-clock</v-icon>
                          </v-avatar>
                        </template>
                        <v-list-item-title>{{ form.shift1_name }}</v-list-item-title>
                        <v-list-item-subtitle>{{ form.shift1_start }} - {{ form.shift1_end }}</v-list-item-subtitle>
                      </v-list-item>
                      
                      <v-list-item v-if="form.shift2_name">
                        <template v-slot:prepend>
                          <v-avatar color="green-lighten-5" size="32">
                            <v-icon color="green">mdi-clock</v-icon>
                          </v-avatar>
                        </template>
                        <v-list-item-title>{{ form.shift2_name }}</v-list-item-title>
                        <v-list-item-subtitle>{{ form.shift2_start }} - {{ form.shift2_end }}</v-list-item-subtitle>
                      </v-list-item>
                      
                      <v-list-item v-if="form.shift3_name">
                        <template v-slot:prepend>
                          <v-avatar color="orange-lighten-5" size="32">
                            <v-icon color="orange">mdi-clock</v-icon>
                          </v-avatar>
                        </template>
                        <v-list-item-title>{{ form.shift3_name }}</v-list-item-title>
                        <v-list-item-subtitle>{{ form.shift3_start }} - {{ form.shift3_end }}</v-list-item-subtitle>
                      </v-list-item>
                      
                      <v-list-item v-if="!form.shift1_name && !form.shift2_name && !form.shift3_name">
                        <v-list-item-title class="text-medium-emphasis">
                          No shifts configured
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-card-text>
                </v-card>
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
                  Edit Shifts
                </v-btn>
                
                <v-btn
                  v-else
                  color="indigo-darken-4"        
                  @click="updateShifts"
                  prepend-icon="mdi-content-save"
                  size="large"
                  class="px-6"
                  :loading="form.processing"
                >
                  Save Shifts
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
import { ref, computed } from "vue";

const page = usePage();
const company = page.props.company;

// Controls whether inputs are editable
const isDisabled = ref(true);

// Store original values for cancel functionality
const originalValues = {
  shift1_name: company.shift1_name || 'Morning Shift',
  shift1_start: company.shift1_start || '08:00',
  shift1_end: company.shift1_end || '16:00',
  shift2_name: company.shift2_name || 'Evening Shift',
  shift2_start: company.shift2_start || '16:00',
  shift2_end: company.shift2_end || '00:00',
  shift3_name: company.shift3_name || 'Night Shift',
  shift3_start: company.shift3_start || '00:00',
  shift3_end: company.shift3_end || '08:00',
  default_shift: company.default_shift || 'shift1',
  shift_change_notice: company.shift_change_notice || 7
};

const form = useForm({
  id: company.id,
  shift1_name: company.shift1_name || 'Morning Shift',
  shift1_start: company.shift1_start || '08:00',
  shift1_end: company.shift1_end || '16:00',
  shift2_name: company.shift2_name || 'Evening Shift',
  shift2_start: company.shift2_start || '16:00',
  shift2_end: company.shift2_end || '00:00',
  shift3_name: company.shift3_name || 'Night Shift',
  shift3_start: company.shift3_start || '00:00',
  shift3_end: company.shift3_end || '08:00',
  default_shift: company.default_shift || 'shift1',
  shift_change_notice: company.shift_change_notice || 7
});

// Computed property for shift options
const shiftOptions = computed(() => {
  const options = [];
  if (form.shift1_name) options.push({ title: form.shift1_name, value: 'shift1' });
  if (form.shift2_name) options.push({ title: form.shift2_name, value: 'shift2' });
  if (form.shift3_name) options.push({ title: form.shift3_name, value: 'shift3' });
  return options;
});

// Cancel edit and restore original values
const cancelEdit = () => {
  Object.keys(originalValues).forEach(key => {
    form[key] = originalValues[key];
  });
  isDisabled.value = true;
};

// Submit update request
const updateShifts = () => {
  form.put(route("company.shifts.update", form.id), {
    preserveScroll: true,
    onSuccess: () => {
      isDisabled.value = true;
      // Update original values after successful save
      Object.keys(originalValues).forEach(key => {
        originalValues[key] = form[key];
      });
      console.log("✅ Shifts updated successfully!");
    },
    onError: (errors) => {
      console.error("❌ Validation failed:", errors);
    },
  });
};
</script>

<style scoped>
.shift-settings-wrapper {
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

/* Enhanced field focus states */
:deep(.v-field--focused) {
  box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
}

/* Smooth transitions */
:deep(.v-field), :deep(.v-btn) {
  transition: all 0.2s ease;
}

/* Disabled state styling */
:deep(.v-field--disabled) {
  opacity: 0.7;
}

/* Button hover effects */
.v-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Card header colors */
:deep(.bg-blue-lighten-5) {
  background-color: rgba(33, 150, 243, 0.1) !important;
}

:deep(.bg-green-lighten-5) {
  background-color: rgba(76, 175, 80, 0.1) !important;
}

:deep(.bg-orange-lighten-5) {
  background-color: rgba(255, 152, 0, 0.1) !important;
}
</style>
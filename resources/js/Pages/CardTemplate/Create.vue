<template>
  <v-dialog v-model="dialog" max-width="600" persistent>
    <v-card rounded="lg" class="template-form-card">
      <!-- Header -->
      <div class="form-header pa-6 pb-4">
        <div class="d-flex align-center justify-space-between">
          <div class="d-flex align-center">
            <v-avatar color="indigo-darken-4" size="48" class="mr-3">
              <v-icon size="28" color="white">
                {{ isEdit ? 'mdi-card-text-outline' : 'mdi-card-plus' }}
              </v-icon>
            </v-avatar>
            <div>
              <h2 class="text-h6 font-weight-bold text-indigo-darken-4">
                {{ isEdit ? 'Edit Template' : 'Create New Template' }}
              </h2>
              <p class="text-caption text-medium-emphasis mb-0">
                {{ isEdit ? 'Update template information' : 'Set up pricing and duration' }}
              </p>
            </div>
          </div>
          <v-btn
            icon="mdi-close"
            variant="text"
            @click="dialog = false"
          ></v-btn>
        </div>
      </div>

      <v-divider></v-divider>

      <!-- Form -->
      <form @submit.prevent="isEdit ? update() : create()">
        <v-card-text class="pa-6">
          <v-row>
            <!-- Card Name -->
            <v-col cols="12">
              <div class="input-group">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block ">
                  <v-icon size="18" class="mr-1">mdi-card-text</v-icon>
                  Card Name
                </label>
                <v-text-field
                  v-model="form.card_name"
                  placeholder="Enter card name (e.g., Weekly Pass)"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.card_name"
                  hide-details="auto"
                      bg-color="indigo-lighten-5"
                  color="indigo-darken-4"
                />
              </div>
            </v-col>

            <!-- Number of Days -->
            <v-col cols="12" sm="6">
              <div class="input-group">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="18" class="mr-1">mdi-calendar-range</v-icon>
                  Duration (Days)
                </label>
                <v-text-field
                  v-model="form.no_of_days"
                  placeholder="Number of days"
                  variant="outlined"
                  density="comfortable"
                  type="number"
                  min="1"
                  :error-messages="form.errors.no_of_days"
                  hide-details="auto"
                bg-color="indigo-lighten-5"
                  color="indigo-darken-4"
                />
              </div>
            </v-col>

            <!-- Price -->
            <v-col cols="12" sm="6">
              <div class="input-group">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="18" class="mr-1">mdi-cash</v-icon>
                  Price
                </label>
                <v-text-field
                  v-model="form.price"
                  placeholder="Enter price"
                  variant="outlined"
                  density="comfortable"
                  type="number"
                  min="0"
                  step="0.01"
                  prefix="₱"
                  :error-messages="form.errors.price"
                  hide-details="auto"
                    bg-color="indigo-lighten-5"
                  color="indigo-darken-4"
                />
              </div>
            </v-col>

            <!-- Discount -->
            <v-col cols="12" sm="6">
              <div class="input-group">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="18" class="mr-1">mdi-tag</v-icon>
                  Discount (Optional)
                </label>
                <v-text-field
                  v-model="form.discount"
                  placeholder="Enter discount"
                  variant="outlined"
                  density="comfortable"
                  type="number"
                  min="0"
                  step="0.01"
                  prefix="₱"
                  :error-messages="form.errors.discount"
                  hide-details="auto"
                bg-color="indigo-lighten-5"
                  color="indigo-darken-4"
                />
              </div>
            </v-col>

            <!-- Final Amount Preview -->
            <v-col cols="12" sm="6">
              <div class="input-group">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="18" class="mr-1">mdi-calculator</v-icon>
                  Final Amount
                </label>
                <v-card class="amount-preview elevation-0" color="indigo-lighten-5" rounded="lg">
                  <div class=" d-flex align-center justify-center">
                    <span class="text-h6 font-weight-bold text-indigo-darken-4">
                      ₱{{ calculateFinalAmount }}
                    </span>
                  </div>
                </v-card>
              </div>
            </v-col>

            <!-- Info Alert -->
            <v-col cols="12">
              <v-alert
                type="info"
                variant="tonal"
                density="compact"
                class="info-alert"
              >
                <div class="text-caption">
                  <strong>Note:</strong> Final amount is calculated as Price - Discount. 
                  This template will be available for card generation.
                </div>
              </v-alert>
            </v-col>
          </v-row>
        </v-card-text>

        <v-divider></v-divider>

        <!-- Actions -->
        <v-card-actions class="pa-6">
          <v-spacer />
          <v-btn
            variant="outlined"
            size="large"
            @click="dialog = false"
            prepend-icon="mdi-close"
          >
            Cancel
          </v-btn>
          <v-btn
            color="indigo-darken-4"
            variant="flat"
            size="large"
            type="submit"
            :loading="form.processing"
            :prepend-icon="isEdit ? 'mdi-content-save' : 'mdi-plus-circle'"
          >
            {{ isEdit ? 'Update Template' : 'Create Template' }}
          </v-btn>
        </v-card-actions>
      </form>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const dialog = defineModel({ type: Boolean, default: false })

const props = defineProps({
  selectedTemplate: { type: Object, default: null }      
})

const isEdit = ref(false)

const form = useForm({
  card_name: '',
  no_of_days: null,
  price: null,
  discount: null,
})

const calculateFinalAmount = computed(() => {
  const price = Number(form.price) || 0
  const discount = Number(form.discount) || 0
  const final = price - discount
  return final.toFixed(2)
})

watch(() => props.selectedTemplate, (val) => {
  if (val) {
    isEdit.value = true
    form.card_name = val.card_name
    form.no_of_days = val.no_of_days
    form.price = val.price
    form.discount = val.discount
  } else {
    isEdit.value = false
    form.reset()
  }
})

const create = () => form.post(route('card-template.store'), {
  onSuccess: () => {
    form.reset()
    dialog.value = false
  },
  onError: (errors) => {
    console.log(errors)
  }
})

const update = () => form.put(route('card-template.update', props.selectedTemplate.id), {
  onSuccess: () => {
    form.reset()
    dialog.value = false
  },
  onError: (errors) => {
    console.log(errors)
  }
})
</script>

<style scoped>
.template-form-card {
  overflow: hidden;
}

.form-header {
 background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
}

.input-group {
  margin-bottom: 0.5rem;
}

.input-label text-indigo-darken-4 font-weight-bold mb-2 d-block {
  display: flex;
  align-items: center;
  font-weight: 600;
  font-size: 0.875rem;
  color: #333;
  margin-bottom: 0.5rem;
}

.input-group :deep(.v-field) {
  border: 2px solid #e0e0e0;
  transition: all 0.3s ease;
}

.input-group :deep(.v-field--focused) {
  border-color: #1a237e;
  box-shadow: 0 0 0 4px rgba(26, 35, 126, 0.1);
}

.input-group :deep(.v-field--error) {
  border-color: #f44336;
}

.amount-preview {
  border: 2px solid #c5cae9;
  min-height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.info-alert {
  border-left: 4px solid #2196f3;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.template-form-card {
  animation: fadeIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 600px) {
  .form-header h2 {
    font-size: 1.125rem;
  }
}
</style>
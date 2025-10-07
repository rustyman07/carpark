<template>
  <v-dialog v-model="dialog" max-width="600" persistent>
    <v-card rounded="lg" class="card-form">
      <!-- Header -->
      <div class="form-header pa-6 pb-4">
        <div class="d-flex align-center justify-space-between">
          <div class="d-flex align-center">
            <v-avatar color="indigo-darken-4" size="48" class="mr-3">
              <v-icon size="28" color="white">mdi-card-plus-outline</v-icon>
            </v-avatar>
            <div>
              <h2 class="text-h6 font-weight-bold text-indigo-darken-4">
                Generate Cards
              </h2>
              <p class="text-caption text-medium-emphasis mb-0">
                Create multiple parking cards from template
              </p>
            </div>
          </div>
          <v-btn
            icon="mdi-close"
            variant="text"
            @click="closeDialog"
          ></v-btn>
        </div>
      </div>

      <v-divider></v-divider>

      <!-- Form -->
      <form @submit.prevent="create()">
        <v-card-text class="pa-6">
          <v-row>
            <!-- Template Selection -->
            <v-col cols="12">
              <div class="input-group">
                <label class="input-label">
                  <v-icon size="18" class="mr-1">mdi-card-text-outline</v-icon>
                  Select Card Template
                </label>
                <v-select
                  v-model="form.card_template_id"
                  :items="props.cardTemplate"
                  item-title="card_name"
                  item-value="id"
                  placeholder="Choose a template"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-format-list-bulleted"
                  hide-details="auto"
                  @update:modelValue="onTemplateChange"
                >
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props">
                      <template v-slot:prepend>
                        <v-avatar color="indigo-lighten-5" size="36">
                          <v-icon size="20" color="indigo-darken-4">mdi-card-text</v-icon>
                        </v-avatar>
                      </template>
                      <template v-slot:subtitle>
                        <span class="text-caption">{{ item.raw.no_of_days }} days - ₱{{ item.raw.amount }}</span>
                      </template>
                    </v-list-item>
                  </template>
                </v-select>
              </div>
            </v-col>

            <!-- Number of Cards -->
            <v-col cols="12">
              <div class="input-group">
                <label class="input-label">
                  <v-icon size="18" class="mr-1">mdi-numeric</v-icon>
                  Number of Cards to Generate
                </label>
                <v-text-field
                  v-model="form.no_of_cards"
                  placeholder="Enter quantity"
                  variant="outlined"
                  density="comfortable"
                  type="number"
                  min="1"
                  :error-messages="form.errors.no_of_cards"
                  prepend-inner-icon="mdi-counter"
                  hide-details="auto"
                />
              </div>
            </v-col>

            <!-- Template Details Preview -->
            <v-col cols="12" v-if="form.card_template_id">
              <v-alert
                type="info"
                variant="tonal"
                density="comfortable"
                class="template-preview"
              >
                <div class="text-subtitle-2 font-weight-bold mb-2">Template Details</div>
                <v-row dense>
                  <v-col cols="6" sm="3">
                    <div class="preview-item">
                      <v-icon size="16" class="mr-1">mdi-calendar-range</v-icon>
                      <span class="text-caption text-medium-emphasis">Duration:</span>
                    </div>
                    <div class="text-body-2 font-weight-bold">{{ form.no_of_days }} days</div>
                  </v-col>
                  <v-col cols="6" sm="3">
                    <div class="preview-item">
                      <v-icon size="16" class="mr-1">mdi-cash</v-icon>
                      <span class="text-caption text-medium-emphasis">Price:</span>
                    </div>
                    <div class="text-body-2 font-weight-bold">₱{{ Number(form.price).toFixed(2) }}</div>
                  </v-col>
                  <v-col cols="6" sm="3">
                    <div class="preview-item">
                      <v-icon size="16" class="mr-1">mdi-tag</v-icon>
                      <span class="text-caption text-medium-emphasis">Discount:</span>
                    </div>
                    <div class="text-body-2 font-weight-bold text-success">₱{{ Number(form.discount).toFixed(2) }}</div>
                  </v-col>
                  <v-col cols="6" sm="3">
                    <div class="preview-item">
                      <v-icon size="16" class="mr-1">mdi-calculator</v-icon>
                      <span class="text-caption text-medium-emphasis">Final:</span>
                    </div>
                    <div class="text-body-2 font-weight-bold text-indigo-darken-4">₱{{ calculateFinalAmount }}</div>
                  </v-col>
                </v-row>
              </v-alert>
            </v-col>

            <!-- Total Cost Preview -->
            <v-col cols="12" v-if="form.card_template_id && form.no_of_cards">
              <v-card class="total-preview elevation-0" color="indigo-lighten-5" rounded="lg">
                <div class="pa-4">
                  <div class="d-flex align-center justify-space-between">
                    <div>
                      <p class="text-caption text-medium-emphasis mb-1">Total Value</p>
                      <p class="text-h5 font-weight-bold text-indigo-darken-4 mb-0">
                        ₱{{ calculateTotalCost }}
                      </p>
                    </div>
                    <v-chip color="indigo-darken-4" variant="flat">
                      <v-icon start size="14">mdi-card-multiple</v-icon>
                      {{ form.no_of_cards }} cards
                    </v-chip>
                  </div>
                  <p class="text-caption text-medium-emphasis mt-2 mb-0">
                    {{ form.no_of_cards }} × ₱{{ calculateFinalAmount }} = ₱{{ calculateTotalCost }}
                  </p>
                </div>
              </v-card>
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
            @click="closeDialog"
            prepend-icon="mdi-close"
            :disabled="form.processing"
          >
            Cancel
          </v-btn>
          <v-btn
            color="indigo-darken-4"
            variant="flat"
            size="large"
            type="submit"
            :loading="form.processing"
            :disabled="!form.card_template_id || !form.no_of_cards"
            prepend-icon="mdi-plus-circle"
          >
            Generate Cards
          </v-btn>
        </v-card-actions>
      </form>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const dialog = defineModel({ type: Boolean, default: false })
const loading = defineModel('loading')
const progress = defineModel('progress')

const props = defineProps({
  cardTemplate: { type: Array, default: () => [] }
})

const isEdit = ref(false)
let progressInterval = null

const form = useForm({
  no_of_cards: null,
  card_template_id: null,
  card_name: '',
  no_of_days: null,
  price: null,
  discount: null
})

const calculateFinalAmount = computed(() => {
  const price = Number(form.price) || 0
  const discount = Number(form.discount) || 0
  return (price - discount).toFixed(2)
})

const calculateTotalCost = computed(() => {
  const finalAmount = Number(calculateFinalAmount.value)
  const quantity = Number(form.no_of_cards) || 0
  return (finalAmount * quantity).toFixed(2)
})

const create = () =>
  form.post(route('card-inventory.store'), {
    onStart: () => {
      loading.value = true
      progress.value = 0

      progressInterval = setInterval(() => {
        if (progress.value < 90) {
          progress.value += 5
        }
      }, 200)
    },
    onSuccess: () => {
      progress.value = 100
      setTimeout(() => {
        dialog.value = false
        form.reset()
      }, 500)
    },
    onError: () => {
      progress.value = 100
    },
    onFinish: () => {
      clearInterval(progressInterval)
      loading.value = false
    }
  })

const closeDialog = () => {
  dialog.value = false
  form.reset()
}

const onTemplateChange = (id) => {
  const selected = props.cardTemplate.find((t) => t.id === id)
  if (selected) {
    form.card_name = selected.card_name
    form.no_of_days = selected.no_of_days
    form.price = selected.price
    form.discount = selected.discount
  }
}
</script>

<style scoped>
.card-form {
  overflow: hidden;
}

.form-header {
  background: linear-gradient(135deg, rgba(26, 35, 126, 0.05) 0%, rgba(26, 35, 126, 0.02) 100%);
}

.input-group {
  margin-bottom: 0.5rem;
}

.input-label {
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

.template-preview {
  border-left: 4px solid #2196f3;
}

.preview-item {
  display: flex;
  align-items: center;
  margin-bottom: 4px;
}

.total-preview {
  border: 2px solid #c5cae9;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
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

.card-form {
  animation: fadeIn 0.3s ease-out;
}
</style>
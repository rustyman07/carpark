<template>
  <v-dialog v-model="dialog" max-width="700" persistent>
    <v-card rounded="lg" class="shift-form-card">
      <!-- Header -->
      <div class="form-header pa-6 pb-4">
        <div class="d-flex align-center justify-space-between">
          <div class="d-flex align-center">
            <v-avatar color="indigo-darken-4" size="48" class="mr-3">
              <v-icon size="28" color="white">
                {{ isEdit ? 'mdi-clock-edit' : 'mdi-clock-plus' }}
              </v-icon>
            </v-avatar>
            <div>
              <h2 class="text-h6 font-weight-bold text-indigo-darken-4">
                {{ isEdit ? 'Edit Shift' : 'Create New Shift' }}
              </h2>
              <p class="text-caption text-medium-emphasis mb-0">
                {{ isEdit ? 'Update shift information and schedule' : 'Set up work shift details' }}
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
      <form @submit.prevent="isEdit ? update() : create()">
        <v-card-text class="pa-6">
          <v-row>
            <!-- Shift Name -->
            <v-col cols="12">
              <div class="input-group">
                <label class="input-label">
                  <v-icon size="18" class="mr-1">mdi-clock-outline</v-icon>
                  Shift Name
                </label>
                <v-text-field
                  v-model="form.name"
                  placeholder="Enter shift name (e.g., Morning Shift)"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.name"
                  prepend-inner-icon="mdi-label"
                  hide-details="auto"
                />
              </div>
            </v-col>

            <!-- Time Selection -->
            <v-col cols="12" sm="6">
              <div class="input-group">
                <label class="input-label">
                  <v-icon size="18" class="mr-1">mdi-clock-start</v-icon>
                  Start Time
                </label>
                <v-text-field
                  v-model="form.start_time"
                  type="time"
                  placeholder="Select start time"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.start_time"
                  prepend-inner-icon="mdi-clock-in"
                  hide-details="auto"
                />
              </div>
            </v-col>

            <v-col cols="12" sm="6">
              <div class="input-group">
                <label class="input-label">
                  <v-icon size="18" class="mr-1">mdi-clock-end</v-icon>
                  End Time
                </label>
                <v-text-field
                  v-model="form.end_time"
                  type="time"
                  placeholder="Select end time"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.end_time"
                  prepend-inner-icon="mdi-clock-out"
                  hide-details="auto"
                />
              </div>
            </v-col>

            <!-- Shift Duration Preview -->
            <v-col cols="12" v-if="form.start_time && form.end_time">
              <v-alert
                type="info"
                variant="tonal"
                density="compact"
                class="duration-preview"
              >
                <div class="d-flex align-center justify-space-between">
                  <div>
                    <div class="text-subtitle-2 font-weight-bold mb-1">Shift Duration</div>
                    <div class="text-body-2">
                      {{ calculateDuration() }}
                    </div>
                  </div>
                  <v-icon size="32" color="info">mdi-clock-time-eight</v-icon>
                </div>
              </v-alert>
            </v-col>

            <!-- Active Status -->
            <v-col cols="12">
              <div class="input-group">
                <label class="input-label">
                  <v-icon size="18" class="mr-1">mdi-toggle-switch</v-icon>
                  Status
                </label>
                <v-card class="status-card elevation-0" rounded="lg">
                  <div class="pa-4">
                    <div class="d-flex align-center justify-space-between">
                      <div>
                        <p class="text-subtitle-2 font-weight-bold mb-1">
                          {{ form.is_active ? 'Active Shift' : 'Inactive Shift' }}
                        </p>
                        <p class="text-caption text-medium-emphasis mb-0">
                          {{ form.is_active ? 'This shift is available for assignment' : 'This shift is hidden and cannot be assigned' }}
                        </p>
                      </div>
                      <v-switch
                        v-model="form.is_active"
                        color="success"
                        hide-details
                        inset
                      ></v-switch>
                    </div>
                  </div>
                </v-card>
              </div>
            </v-col>

            <!-- Staff Assignment (optional) -->
            <v-col cols="12" v-if="availableUsers && availableUsers.length">
              <div class="input-group">
                <label class="input-label">
                  <v-icon size="18" class="mr-1">mdi-account-multiple</v-icon>
                  Assign Staff (Optional)
                </label>
                <v-select
                  v-model="form.user_ids"
                  :items="availableUsers"
                  item-title="name"
                  item-value="id"
                  placeholder="Select staff members"
                  variant="outlined"
                  density="comfortable"
                  multiple
                  chips
                  closable-chips
                  prepend-inner-icon="mdi-account-group"
                  hide-details="auto"
                >
                  <template v-slot:chip="{ props, item }">
                    <v-chip
                      v-bind="props"
                      :text="item.title"
                      color="indigo-lighten-5"
                    >
                      <template v-slot:prepend>
                        <v-avatar color="indigo-darken-4" size="24">
                          <v-icon size="14" color="white">mdi-account</v-icon>
                        </v-avatar>
                      </template>
                    </v-chip>
                  </template>

                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props">
                      <template v-slot:prepend>
                        <v-avatar color="indigo-lighten-5" size="36">
                          <v-icon size="20" color="indigo-darken-4">mdi-account</v-icon>
                        </v-avatar>
                      </template>
                    </v-list-item>
                  </template>
                </v-select>
              </div>
            </v-col>

            <!-- Info Note -->
            <v-col cols="12">
              <v-alert
                type="success"
                variant="tonal"
                density="compact"
                class="info-note"
              >
                <div class="text-caption">
                  <strong>Note:</strong> Staff members can be assigned to multiple shifts. Make sure the shift times don't overlap with their other assignments.
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
            @click="isEdit ? update() : create()"
            :loading="form.processing"
            :prepend-icon="isEdit ? 'mdi-content-save' : 'mdi-plus-circle'"
          >
            {{ isEdit ? 'Update Shift' : 'Create Shift' }}
          </v-btn>
        </v-card-actions>
      </form>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import dayjs from 'dayjs'

const dialog = defineModel({ type: Boolean, default: false })

const props = defineProps({
  selectedShift: { type: Object, default: null },
  availableUsers: { type: Array, default: () => [] }
})

const isEdit = ref(false)

const form = useForm({
  name: '',
  start_time: '',
  end_time: '',
  is_active: true,
  user_ids: []
})

const calculateDuration = () => {
  if (!form.start_time || !form.end_time) return ''

  const start = dayjs(`2000-01-01 ${form.start_time}`)
  let end = dayjs(`2000-01-01 ${form.end_time}`)

  // Handle overnight shifts
  if (end.isBefore(start)) {
    end = end.add(1, 'day')
  }

  const hours = end.diff(start, 'hour')
  const minutes = end.diff(start, 'minute') % 60

  if (hours === 0) {
    return `${minutes} minutes`
  } else if (minutes === 0) {
    return `${hours} hours`
  } else {
    return `${hours} hours ${minutes} minutes`
  }
}

// 🛠 Watch for edit mode
watch(
  () => props.selectedShift,
  (val) => {
    if (val && Object.keys(val).length) {
      isEdit.value = true
      form.name = val.name
      form.start_time = val.start_time?.slice(0, 5) || '' // ⏱ normalize HH:mm
      form.end_time = val.end_time?.slice(0, 5) || ''
      form.is_active = val.is_active
      form.user_ids = val.users?.map(u => u.id) || []
    } else {
      isEdit.value = false
      form.reset()
      form.is_active = true
    }
  },
  { immediate: true }
)

// ✅ Normalize time to HH:mm before sending
const normalizeTime = (time) => {
  return time ? dayjs(`2000-01-01 ${time}`).format('HH:mm') : null
}

const create = () => {
  form.start_time = normalizeTime(form.start_time)
  form.end_time = normalizeTime(form.end_time)

  form.post(route('shift.store'), {
    onSuccess: () => {
      form.reset()
      form.is_active = true
      dialog.value = false
    },
    onError: (errors) => {
      console.log(errors)
    }
  })
}

const update = () => {
  form.start_time = normalizeTime(form.start_time)
  form.end_time = normalizeTime(form.end_time)

  form.put(route('shifts.update', props.selectedShift.id), {
    onSuccess: () => {
      dialog.value = false
    },
    onError: (errors) => {
      console.log(errors)
    }
  })
}

const closeDialog = () => {
  form.reset()
  form.is_active = true
  dialog.value = false
}
</script>

<style scoped>
.shift-form-card {
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

.duration-preview {
  border-left: 4px solid #2196f3;
}

.status-card {
  border: 2px solid #e0e0e0;
  transition: all 0.3s ease;
}

.status-card:hover {
  border-color: #1a237e;
  box-shadow: 0 4px 12px rgba(26, 35, 126, 0.1);
}

.info-note {
  border-left: 4px solid #4caf50;
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

.shift-form-card {
  animation: fadeIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 600px) {
  .form-header h2 {
    font-size: 1.125rem;
  }
}
</style>
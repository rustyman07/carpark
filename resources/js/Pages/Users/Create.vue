<template>
  <v-dialog v-model="dialog" max-width="600" persistent>
    <v-card rounded="lg" class="user-form-card">
      <!-- Header -->
      <div class="form-header pa-6 pb-4">
        <div class="d-flex align-center justify-space-between">
          <div class="d-flex align-center">
            <v-avatar color="indigo-darken-4" size="48" class="mr-3">
              <v-icon size="28" color="white">
                {{ isEdit ? 'mdi-account-edit' : 'mdi-account-plus' }}
              </v-icon>
            </v-avatar>
            <div>
              <h2 class="text-h6 font-weight-bold text-indigo-darken-4">
                {{ isEdit ? 'Edit User' : 'Create New User' }}
              </h2>
              <p class="text-caption text-medium-emphasis mb-0">
                {{ isEdit ? 'Update user information' : 'Fill in the details below' }}
              </p>
            </div>
          </div>
          <v-btn icon="mdi-close" variant="text" @click="dialog = false"></v-btn>
        </div>
      </div>

      <v-divider></v-divider>

      <!-- Form -->
      <form @submit.prevent="isEdit ? update() : create()">
        <v-card-text class="pa-6">
          <v-row>
            <!-- Username -->
            <v-col cols="12">
              <div class="input-group">
                <label class="input-label"><v-icon size="18" class="mr-1">mdi-at</v-icon> Username</label>
                <v-text-field
                  v-model="form.username"
                  placeholder="Enter username"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.username"
                  prepend-inner-icon="mdi-account-circle"
                  hide-details="auto"
                />
              </div>
            </v-col>

            <!-- Password (create only) -->
            <v-col cols="12" v-if="!isEdit">
              <div class="input-group">
                <label class="input-label"><v-icon size="18" class="mr-1">mdi-lock</v-icon> Password</label>
                <v-text-field
                  v-model="form.password"
                  placeholder="Enter password"
                  variant="outlined"
                  density="comfortable"
                  :type="showPassword ? 'text' : 'password'"
                  :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  @click:append-inner="showPassword = !showPassword"
                  :error-messages="form.errors.password"
                  prepend-inner-icon="mdi-lock-outline"
                  hide-details="auto"
                />
              </div>
            </v-col>

            <!-- Full Name -->
            <v-col cols="12">
              <div class="input-group">
                <label class="input-label"><v-icon size="18" class="mr-1">mdi-account</v-icon> Full Name</label>
                <v-text-field
                  v-model="form.name"
                  placeholder="Enter full name"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.name"
                  prepend-inner-icon="mdi-account-outline"
                  hide-details="auto"
                />
              </div>
            </v-col>

            <!-- Role -->
            <v-col cols="12" sm="6">
              <div class="input-group">
                <label class="input-label"><v-icon size="18" class="mr-1">mdi-shield-account</v-icon> Role</label>
                <v-select
                  v-model="form.role"
                  :items="role"
                  item-title="name"
                  item-value="id"
                  placeholder="Select role"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-shield-check"
                  hide-details="auto"
                />
              </div>
            </v-col>

            <!-- Contact -->
            <v-col cols="12" sm="6">
              <div class="input-group">
                <label class="input-label"><v-icon size="18" class="mr-1">mdi-phone</v-icon> Contact</label>
                <v-text-field
                  v-model="form.contact"
                  placeholder="Enter contact number"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.contact"
                  prepend-inner-icon="mdi-phone-outline"
                  hide-details="auto"
                />
              </div>
            </v-col>

            <!-- 🕐 Assign Shift -->
            <v-col cols="12">
              <div class="input-group">
                <label class="input-label"><v-icon size="18" class="mr-1">mdi-clock-outline</v-icon> Assign Shift</label>
                <v-select
                  v-model="form.shift_id"
                  :items="shifts"
                  item-title="name"
                  item-value="id"
                  placeholder="Select a shift"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-clock-time-eight-outline"
                  hide-details="auto"
                  :error-messages="form.errors.shift_id"
                />
              </div>
            </v-col>

            <!-- Reset Password (edit only) -->
            <v-col cols="12" v-if="isEdit && props.selectedItem.id">
              <v-alert type="info" variant="tonal" density="compact" class="reset-password-alert">
                <div class="d-flex align-center justify-space-between">
                  <div>
                    <div class="font-weight-medium mb-1">Password Management</div>
                    <div class="text-caption">Need to change the user's password?</div>
                  </div>
                  <Link :href="route('users.reset-password.form', { user: props.selectedItem.id })" class="reset-link">
                    <v-btn color="info" variant="flat" size="small" prepend-icon="mdi-lock-reset">
                      Reset Password
                    </v-btn>
                  </Link>
                </div>
              </v-alert>
            </v-col>
          </v-row>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions class="pa-6">
          <v-spacer />
          <v-btn variant="outlined" size="large" @click="dialog = false" prepend-icon="mdi-close">Cancel</v-btn>
          <v-btn color="indigo-darken-4" variant="flat" size="large" type="submit" :loading="form.processing" :prepend-icon="isEdit ? 'mdi-content-save' : 'mdi-plus-circle'">
            {{ isEdit ? 'Update User' : 'Create User' }}
          </v-btn>
        </v-card-actions>
      </form>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const dialog = defineModel({ type: Boolean, default: false })
const props = defineProps({
  selectedItem: { type: Object, default: () => ({}) },
  shifts: { type: Array, default: () => [] } // 🆕 Get shifts from backend
})

const isEdit = ref(false)
const showPassword = ref(false)

const form = useForm({
  name: '',
  username: '',
  password: '',
  role: null,
  contact: '',
  shift_id: null // 🆕 added
})

const role = [
  { id: 1, name: 'Admin' },
  { id: 2, name: 'Staff' }
]

watch(
  () => props.selectedItem,
  (val) => {
    if (val && Object.keys(val).length) {
      isEdit.value = true
      form.name = val.name
      form.username = val.username
      form.role = val.role
      form.contact = val.contact
      form.shift_id = val.shift_id ?? null
    } else {
      isEdit.value = false
      form.reset()
    }
  },
  { immediate: true }
)

const create = () =>
  form.post(route('users.store'), {
    onSuccess: () => {
      form.reset()
      dialog.value = false
    }
  })

const update = () =>
  form.put(route('users.update', props.selectedItem.id), {
    onSuccess: () => {
      dialog.value = false
    }
  })
</script>

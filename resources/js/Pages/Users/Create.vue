<template>
  <v-dialog v-model="dialog" max-width="700" persistent scrollable>
    <v-card rounded="lg" class="user-form-card elevation-24">
      <!-- Header -->
      <div class="form-header pa-6 pb-4">
        <div class="d-flex align-center justify-space-between">
          <div class="d-flex align-center">
            <v-avatar 
              :color="isEdit ? 'indigo-darken-3' : 'indigo-darken-4'" 
              size="48" 
              class="mr-4 elevation-8"
            >
              <v-icon size="28" color="white">
                {{ isEdit ? 'mdi-account-edit' : 'mdi-account-plus' }}
              </v-icon>
            </v-avatar>
            <div>
              <h2 class="text-h5 font-weight-bold text-indigo-darken-4 mb-1">
                {{ isEdit ? 'Edit User' : 'Create New User' }}
              </h2>
              <p class="text-body-2 text-grey-darken-1 mb-0">
                {{ isEdit ? 'Update user information and settings' : 'Fill in the details to add a new user' }}
              </p>
            </div>
          </div>
          <v-btn 
            icon="mdi-close" 
            variant="text" 
            size="large"
            color="grey-darken-2"
            @click="dialog = false"
          ></v-btn>
        </div>
      </div>

      <v-divider class="border-opacity-100" color="indigo-lighten-4"></v-divider>

      <!-- Form -->
      <form @submit.prevent="isEdit ? update() : create()">
        <v-card-text class="pa-8" style="max-height: 600px; overflow-y: auto;">
          <v-row>
            <!-- Username -->
            <v-col cols="12">
              <div class="input-group mb-2">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="20" class="mr-2" color="indigo-darken-4">mdi-at</v-icon> 
                  Username
                </label>
                <v-text-field
                v-model="form.username"
                placeholder="Enter username"
                variant="outlined"
                density="compact"
                :error-messages="form.errors.username"
                prepend-inner-icon="mdi-account-circle"
                hide-details="auto"
                rounded="lg"
                bg-color="indigo-lighten-5"
                color="indigo-darken-4"
                @input="form.username = form.username.replace(/\s+/g, '')"
                />

              </div>
            </v-col>

            <!-- Password (create only) -->
            <v-col cols="12" v-if="!isEdit">
              <div class="input-group mb-2">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="20" class="mr-2" color="indigo-darken-4">mdi-lock</v-icon> 
                  Password
                </label>
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
                  rounded="lg"
                  bg-color="indigo-lighten-5"
                  color="indigo-darken-4"
                />
              </div>
            </v-col>

            <!-- Full Name -->
            <v-col cols="12">
              <div class="input-group mb-2">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="20" class="mr-2" color="indigo-darken-4">mdi-account</v-icon> 
                  Full Name
                </label>
                <v-text-field
                  v-model="form.name"
                  placeholder="Enter full name"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.name"
                  prepend-inner-icon="mdi-account-outline"
                  hide-details="auto"
                  rounded="lg"
                  bg-color="indigo-lighten-5"
                  color="indigo-darken-4"
                />
              </div>
            </v-col>

            <!-- Role -->
            <v-col cols="12" sm="6">
              <div class="input-group mb-2">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="20" class="mr-2" color="indigo-darken-4">mdi-shield-account</v-icon> 
                  Role
                </label>
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
                  rounded="lg"
                  bg-color="indigo-lighten-5"
                  color="indigo-darken-4"
                >
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props">
                      <template v-slot:prepend>
                        <v-icon :color="getRoleColor(item.raw.id)">
                          {{ getRoleIcon(item.raw.id) }}
                        </v-icon>
                      </template>
                    </v-list-item>
                  </template>
                  <template v-slot:selection="{ item }">
                    <div class="d-flex align-center">
                      <v-icon :color="getRoleColor(item.raw.id)" size="20" class="mr-2">
                        {{ getRoleIcon(item.raw.id) }}
                      </v-icon>
                      {{ item.title }}
                    </div>
                  </template>
                </v-select>
              </div>
            </v-col>

            <!-- Contact -->
            <v-col cols="12" sm="6">
              <div class="input-group mb-2">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="20" class="mr-2" color="indigo-darken-4">mdi-phone</v-icon> 
                  Contact
                </label>
                <v-text-field
                  v-model="form.contact"
                  placeholder="Enter contact number"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.contact"
                  prepend-inner-icon="mdi-phone-outline"
                  hide-details="auto"
                  rounded="lg"
                  bg-color="indigo-lighten-5"
                  color="indigo-darken-4"
                />
              </div>
            </v-col>

            <!-- Assign Shift -->
            <!-- <v-col cols="12">
              <div class="input-group mb-2">
                <label class="input-label text-indigo-darken-4 font-weight-bold mb-2 d-block">
                  <v-icon size="20" class="mr-2" color="indigo-darken-4">mdi-clock-outline</v-icon> 
                  Assign Shift
                </label>
                <v-select
                  v-model="form.shift_id"
                  :items="shifts"
                  item-title="name"
                  item-value="id"
                  placeholder="Select a shift (optional)"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-clock-time-eight-outline"
                  hide-details="auto"
                  :error-messages="form.errors.shift_id"
                  rounded="lg"
                  bg-color="indigo-lighten-5"
                  color="indigo-darken-4"
                  clearable
                />
              </div>
            </v-col> -->

            <!-- Reset Password (edit only) -->
            <v-col cols="12" v-if="isEdit && props.selectedItem.id">
              <v-alert 
                type="info" 
                variant="tonal" 
                density="comfortable" 
                rounded="lg"
                class="reset-password-alert elevation-2"
                border="start"
                border-color="indigo-darken-4"
              >
                <div class="d-flex align-center justify-space-between flex-wrap ga-3">
                  <div>
                    <div class="font-weight-bold text-indigo-darken-4 mb-1">
                      <v-icon size="20" class="mr-2">mdi-lock-reset</v-icon>
                      Password Management
                    </div>
                    <div class="text-body-2 text-grey-darken-1">
                      Need to change this user's password?
                    </div>
                  </div>
                  <Link :href="route('users.reset-password.form', { user: props.selectedItem.id })">
                    <v-btn 
                      color="indigo-darken-4" 
                      variant="flat" 
                      size="default" 
                      prepend-icon="mdi-lock-reset"
                      rounded="lg"
                      class="elevation-4"
                    >
                      Reset Password
                    </v-btn>
                  </Link>
                </div>
              </v-alert>
            </v-col>
          </v-row>
        </v-card-text>

        <v-divider class="border-opacity-100" color="indigo-lighten-4"></v-divider>

        <v-card-actions class="pa-4">
          <v-spacer />
          <v-btn 
            variant="outlined" 
            size="x-large" 
            @click="dialog = false" 
            prepend-icon="mdi-close"
            rounded="lg"
            color="indigo-darken-2"
            class="px-6"
          >
            Cancel
          </v-btn>
          <v-btn 
            color="indigo-darken-4" 
            variant="flat" 
            size="x-large" 
            type="submit" 
            :loading="form.processing" 
            :prepend-icon="isEdit ? 'mdi-content-save' : 'mdi-plus-circle'"
            rounded="lg"
            class="elevation-8 px-6"
          >
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
  shifts: { type: Array, default: () => [] }
})

const isEdit = ref(false)
const showPassword = ref(false)

const form = useForm({
  name: '',
  username: '',
  password: '',
  role: null,
  contact: '',
  shift_id: null
})

const role = [
  { id: 1, name: 'Admin' },
  { id: 2, name: 'Staff' },
  { id: 3, name: 'Auditor' }
]

const getRoleColor = (roleId) => {
  const colors = {
    1: 'red-darken-2',
    2: 'blue-darken-2',
    3: 'purple-darken-2'
  }
  return colors[roleId] || 'grey'
}

const getRoleIcon = (roleId) => {
  const icons = {
    1: 'mdi-shield-crown',
    2: 'mdi-account',
    3: 'mdi-file-search'
  }
  return icons[roleId] || 'mdi-account'
}

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

<style scoped>
.user-form-card {
  background: white;
  border: 3px solid #e8eaf6;
}

.form-header {
  background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
}

.input-group {
  position: relative;
}

.input-label {
  display: flex;
  align-items: center;
  font-size: 0.875rem;
}

.reset-password-alert {
  background: linear-gradient(135deg, #e8eaf6 0%, #f3e5f5 100%);
}

:deep(.v-text-field--outlined) {
  transition: all 0.3s ease;
}

:deep(.v-text-field--outlined:hover) {
  transform: translateY(-1px);
}

:deep(.v-text-field--outlined.v-field--focused) {
  box-shadow: 0 4px 12px rgba(57, 73, 171, 0.2);
}

:deep(.v-select.v-field--focused) {
  box-shadow: 0 4px 12px rgba(57, 73, 171, 0.2);
}

@media (max-width: 600px) {
  .form-header {
    padding: 1.5rem !important;
  }
  
  .form-header .v-avatar {
    width: 48px !important;
    height: 48px !important;
    margin-right: 0.75rem !important;
  }
  
  .reset-password-alert .d-flex {
    flex-direction: column;
    align-items: stretch !important;
  }
  
  .reset-password-alert .v-btn {
    width: 100%;
  }
}
</style>
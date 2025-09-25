<template>
  <v-dialog v-model="dialog" max-width="350">
    <form @submit.prevent="isEdit ? update() : create()">
      <v-card title="Users">
        <v-card-text>

            <v-text-field
              v-model="form.username"
              label="Username"
              variant="underlined"
              :error-messages="form.errors.username"
            />
            <v-text-field
            v-model="form.password"
            label="Password"
            variant="underlined"
            :type="showPassword ? 'text' : 'password'"
            :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
            @click:append-inner="showPassword = !showPassword"
            :error-messages="form.errors.password"
            />

            <v-select
            v-model="form.role"
            :items="role"
            item-title="name"
            item-value="id"
            variant="underlined"
            />

              
            <v-text-field
              v-model="form.name"
              label="Name"
              variant="underlined"
              :error-messages="form.errors.name"
            />

          <v-text-field
            v-model="form.contact"
            label="Contact"
            variant="underlined"
            :error-messages="form.errors.contact"
          />
        </v-card-text>

        <v-divider />

        <v-card-actions>
          <v-spacer />
          <v-btn text variant="plain" @click="dialog = false">Close</v-btn>
          <v-btn
            color="primary"
            :text="isEdit ? 'UPDATE' : 'SAVE'"
            variant="tonal"
            type="submit"
          />
        </v-card-actions>
      </v-card>
    </form>
  </v-dialog>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const dialog = defineModel({ type: Boolean, default: false })
const props = defineProps({
  selectedItem: { type: Object, default: () => ({}) 

}

})

const isEdit = ref(false)
const form = useForm({
  name: '',
  username: '',
  password: '',
  role: null,
  contact: ''
})


const role = [
    {id : 1 , name : 'Administrator',
    },
     {id : 2 , name : 'Staff'
    }

]




// Populate form on edit
watch(
  () => props.selectedItem,
  (val) => {
    if (val && Object.keys(val).length) {
      isEdit.value = true
      form.name = val.name
      form.username = val.username
      form.password = val.password
      form.role   = val.role,
      form.contact = val.contact
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
      router.reload({ only: ['salesPerson'], preserveState: true })
    }
  })

const update = () =>
  form.put(route('sales-person.update', props.selectedItem.id), {
    onSuccess: () => {
      dialog.value = false
      router.reload({ only: ['salesPerson'], preserveState: true })
    }
  })
</script>

<template>
  <v-dialog v-model="dialog" max-width="600">
    <form @submit.prevent="isEdit ? update() : create()">
      <v-card title="Sales Person">
        <v-card-text>
          <v-row>
            <v-text-field
              v-model="form.firstname"
              label="First Name"
              variant="underlined"
              :error-messages="form.errors.firstname"
            />
            <v-text-field
              v-model="form.lastname"
              label="Last Name"
              variant="underlined"
              :error-messages="form.errors.lastname"
            />
            <v-text-field
              v-model="form.middlename"
              label="Middle Name"
              variant="underlined"
              :error-messages="form.errors.middlename"
            />
          </v-row>

          <v-text-field
            v-model="form.address"
            label="Address"
            variant="underlined"
            :error-messages="form.errors.address"
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
  selectedItem: { type: Object, default: () => ({}) }
})

const isEdit = ref(false)
const form = useForm({
  firstname: '',
  lastname: '',
  middlename: '',
  address: '',
  contact: ''
})

// Populate form on edit
watch(
  () => props.selectedItem,
  (val) => {
    if (val && Object.keys(val).length) {
      isEdit.value = true
      form.firstname = val.firstname
      form.lastname = val.lastname
      form.middlename = val.middlename
      form.address = val.address
      form.contact = val.contact
    } else {
      isEdit.value = false
      form.reset()
    }
  },
  { immediate: true }
)

const create = () =>
  form.post(route('sales-person.store'), {
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

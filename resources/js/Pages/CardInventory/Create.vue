<!-- Create.vue -->
<template>
  <v-dialog v-model="dialog" max-width="600">
    <form @submit.prevent="isEdit ? update() : create()">
      <v-card title="TEMPLATE">
        <v-card-text>
          <v-select
            v-model="form.card_template_id"
            :items="props.cardTemplate"
            item-title="card_name"
            item-value="id"
            @update:modelValue="onTemplateChange"
          />

          <v-text-field
            v-model="form.card_name"
            variant="underlined"
            label="Card Name"
            type="text"
            :error-messages="form.errors.card_name"
          />
          <v-text-field
            v-model="form.no_of_cards"
            variant="underlined"
            label="No. of Cards"
            type="number"
            :error-messages="form.errors.no_of_cards"
          />
          <v-text-field
            v-model="form.no_of_days"
            variant="underlined"
            label="No. of Days"
            type="number"
            :error-messages="form.errors.no_of_days"
          />
          <v-text-field
            v-model="form.price"
            variant="underlined"
            label="Price"
            type="number"
            :error-messages="form.errors.price"
          />
          <v-text-field
            v-model="form.discount"
            variant="underlined"
            label="Discount"
            type="number"
            :error-messages="form.errors.discount"
          />
        </v-card-text>

        <v-divider />

        <v-card-actions>
          <v-spacer />
          <v-btn text="Close" variant="plain" @click="dialog = false" />
          <v-btn
            color="primary"
            :text="isEdit ? 'UPDATE' : 'SAVE'"
            variant="tonal"
            type="submit"
            :disabled="form.processing"
          />
        </v-card-actions>
      </v-card>
    </form>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

// 🔹 dialog state
const dialog = defineModel({ type: Boolean, default: false })
const loading = defineModel('loading')
const progress = defineModel('progress') 

const props = defineProps({
  cardTemplate: { type: Array, default: () => [] }
})

const isEdit = ref(false)



let progressInterval = null

// 🔹 form
const form = useForm({
  no_of_cards: 1,
  card_template_id: null,
  card_name: '',
  no_of_days: 1,
  price: 0,
  discount: null
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

const update = () =>
  form.put(route('card-inventory.update', props.selectedTemplate.id), {
    onSuccess: () => {
      form.reset()
      dialog.value = false
    },
    onError: (errors) => {
      console.log(errors)
    }
  })

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

<template>
  <div class="max-w-md mx-auto mt-12">
    <h1 class="text-2xl font-semibold mb-6">Reset Password for {{ user.username }}</h1>

    <form @submit.prevent="submit">
      <v-text-field
        v-model="form.password"
        label="New Password"
        type="password"
        :error-messages="form.errors.password"
      />

      <v-text-field
        v-model="form.password_confirmation"
        label="Confirm Password"
        type="password"
      />

      <v-btn color="primary" type="submit" class="mt-4">Update Password</v-btn>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
const props = defineProps({ user: Object })

const form = useForm({
  password: '',
  password_confirmation: ''
})

const submit = () => {
  form.post(route('users.reset-password', props.user.id))
}
</script>

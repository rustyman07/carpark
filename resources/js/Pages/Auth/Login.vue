<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineOptions({
    layout: GuestLayout,
});

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <Head title="Log in" />

  <div class="  text-4xl md:text-6xl text-white font-bold my-8"> CarPark System</div>

<div
  class="flex flex-col md:flex-row bg-white shadow-md sm:rounded-lg  md:w-[70%] max-w-[760px] min-w-[320px] mx-auto min-h-[350px] overflow-hidden"
>

    <!-- Left image side -->
    <div
      class="w-full md:w-1/2 h-48 md:h-auto bg-[url('/images/carpark.png')] bg-cover bg-center rounded-t-lg md:rounded-l-lg md:rounded-tr-none"
    ></div>

    <!-- Right form side -->
    <div class="w-full md:w-1/2 p-8 flex flex-col justify-center">
      <form @submit.prevent="submit">
        <!-- Username -->
        <div>
          <InputLabel for="username" value="Username" />
          <TextInput
            id="username"
            type="text"
            class="mt-1 block w-full bg-gray-100 rounded-md border-none focus:ring-0 focus:outline-none"
            v-model="form.username"
            required
            autofocus
            autocomplete="username"
          />
          <InputError class="mt-2" :message="form.errors.username" />
        </div>

        <!-- Password -->
        <div class="mt-4">
          <InputLabel for="password" value="Password" />
          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full bg-gray-100 rounded-md border-none focus:ring-0 focus:outline-none"
            v-model="form.password"
            required
            autocomplete="current-password"
          />
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <!-- Submit button -->
        <div class="mt-4 flex items-center justify-end">
          <PrimaryButton
            class="ms-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Log in
          </PrimaryButton>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineOptions({ layout: GuestLayout });

defineProps({
  canResetPassword: Boolean,
  status: String,
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

  <div>
    <!-- Header -->
    <div class="mb-8 md:mb-12 text-center">
      <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-indigo-900 ">
        CarPark System
      </h1>
    </div>

    <!-- Main Card -->
    <div class="max-w-md mx-auto">
      <div class="p-8 md:p-10 flex items-center">
        <div class="w-full mx-auto">
          <div class="mb-10">
            <h2 class="text-3xl font-bold text-indigo-900 mb-2">Welcome Back</h2>
            <p class="text-gray-600 text-base">Please sign in to access your dashboard</p>
          </div>

          <form @submit.prevent="submit" class="flex flex-col gap-6">
            <!-- Username -->
            <div class="flex flex-col">
              <div class="flex items-center mb-2 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-[18px] h-[18px] text-indigo-900">
                  <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                <InputLabel for="username" value="Username" class="!m-0 !font-semibold !text-gray-800 !text-[0.95rem]" />
              </div>

 <input
  id="username"
  type="text"
   class="mt-1 block w-full p-3.5 border-2  text-base transition-all duration-300 bg-gray-50 focus:bg-white  focus:outline-none focus:ring-2 focus:ring-indigo-900  placeholder:text-gray-400"
  v-model="form.username"
  required
  autofocus
  autocomplete="username"
  placeholder="Enter your username"
/>


              <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <!-- Password -->
            <div class="flex flex-col">
              <div class="flex items-center mb-2 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-[18px] h-[18px] text-indigo-900">
                  <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                </svg>
                <InputLabel for="password" value="Password" class="!m-0 !font-semibold !text-gray-800 !text-[0.95rem]" />
              </div>

              <TextInput
                id="password"
                type="password"
                class="mt-1 block w-full p-3.5 border-2  text-base transition-all duration-300 bg-gray-50 focus:bg-white  focus:outline-none focus:ring-2 focus:ring-indigo-900  placeholder:text-gray-400"
                v-model="form.password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
              />

              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Remember & Forgot -->
            <div class="flex justify-between items-center">
              <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600">
                <Checkbox v-model="form.remember" />
                <span>Remember me</span>
              </label>

              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-sm text-indigo-900 font-semibold no-underline transition-colors duration-200 hover:text-indigo-800"
              >
                Forgot password?
              </Link>
            </div>

            <!-- Submit Button -->
            <PrimaryButton
              class="w-full p-4 bg-gradient-to-br from-indigo-900 to-indigo-800 border-0 rounded text-lg font-bold cursor-pointer transition-all duration-300 flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-[0_10px_30px_rgba(26,35,126,0.4)] disabled:hover:transform-none disabled:hover:shadow-none"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
              </svg>
              <span v-if="form.processing">Signing in...</span>
              <span v-else>Sign In</span>
            </PrimaryButton>
          </form>

          <!-- Footer -->
          <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-400">Powered by CarPark Management v1.0</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes float {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
}
.animate-float { animation: float 25s infinite ease-in-out; }
.animate-float-delayed { animation: float 20s infinite ease-in-out 5s; }
.animate-float-slow { animation: float 30s infinite ease-in-out 10s; }
</style>

<!-- resources/js/Pages/Auth/Login.vue -->
<script setup>
import { ref } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineOptions({ layout: AuthLayout });

// Initialize Inertia Reactive Form
const form = useForm({
  username: '',
  password: '',
});

// State for toggling password visibility
const showPassword = ref(false);

const submitLogin = () => {
  form.post(route('login.attempt'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Student Sign In - Polibatam TalentHub" />

  <div class="space-y-2 text-center sm:text-left">
    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Sign In to TalentHub</h2>
    <p class="text-sm text-slate-500 font-medium">Use your official Polibatam institutional login credentials.</p>
  </div>

  <!-- Flash Alert Message Error (From Laravel validation redirect) -->
  <div v-if="$page.props.flash?.error" class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-semibold text-rose-600">
    {{ $page.props.flash.error }}
  </div>

  <form @submit.prevent="submitLogin" class="space-y-5">
    <!-- Username / NIM Input -->
    <div class="space-y-1.5">
      <label for="username" class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Username / NIM</label>
      <input
        id="username"
        v-model="form.username"
        type="text"
        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-hidden focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition placeholder:text-slate-400"
        placeholder="Example: 33124010xx"
        required
      />
      <span v-if="form.errors.username" class="text-xs font-semibold text-rose-500 block mt-0.5">{{ form.errors.username }}</span>
    </div>

    <!-- Password Input -->
    <div class="space-y-1.5">
      <div class="flex items-center justify-between">
        <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Password</label>
      </div>
      <div class="relative">
        <input
          id="password"
          v-model="form.password"
          :type="showPassword ? 'text' : 'password'"
          class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-hidden focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition pr-10"
          placeholder="••••••••"
          required
        />
        <!-- Show/Hide Password Toggle Button -->
        <button
          type="button"
          @click="showPassword = !showPassword"
          class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer"
        >
          <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.4M9.9 4.24a9.122 9.122 0 012.1-.24c4.478 0 8.268 2.943 9.542 7a10.028 10.028 0 01-4.88 5.85m-5.009-5.009a3.5 3.5 0 11-4.95-4.95l4.95 4.95zM3 3l18 18"/></svg>
        </button>
      </div>
      <span v-if="form.errors.password" class="text-xs font-semibold text-rose-500 block mt-0.5">{{ form.errors.password }}</span>
    </div>

    <!-- Submit Action Button -->
    <button
      type="submit"
      :disabled="form.processing"
      class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold rounded-xl text-sm shadow-md shadow-blue-600/10 transition flex items-center justify-center space-x-2 cursor-pointer"
    >
      <span v-if="form.processing" class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
      <span>Sign In to Portal</span>
    </button>
  </form>

  <!-- Alternative Portal Gateway Links -->
  <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs gap-2 font-medium">
    <span class="text-slate-400">Not a Polibatam Student?</span>
    <Link :href="route('login.industry')" class="text-blue-600 hover:underline font-bold">Corporate / Industry Portal &rarr;</Link>
  </div>
</template>

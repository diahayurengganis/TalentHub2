<!-- resources/js/Pages/Auth/LoginIndustry.vue -->
<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineOptions({ layout: AuthLayout });

// Initialize Inertia Reactive Form
const form = useForm({
  username: '',
  password: '',
});

const submitIndustryLogin = () => {
  form.post(route('login.industry.attempt'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Corporate Sign In - Polibatam TalentHub" />

  <div class="space-y-2 text-center sm:text-left">
    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Corporate Partner Portal</h2>
    <p class="text-sm text-slate-500 font-medium">Sign in to manage your internship positions and career openings.</p>
  </div>

  <!-- Flash Alert Message Error (From Laravel validation redirect) -->
  <div v-if="$page.props.flash?.error" class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-semibold text-rose-600">
    {{ $page.props.flash.error }}
  </div>

  <form @submit.prevent="submitIndustryLogin" class="space-y-5">
    <!-- Corporate Username Input -->
    <div class="space-y-1.5">
      <label for="username" class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Company Username / ID</label>
      <input
        id="username"
        v-model="form.username"
        type="text"
        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-hidden focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition placeholder:text-slate-400"
        placeholder="Enter your corporate username"
        required
      />
      <span v-if="form.errors.username" class="text-xs font-semibold text-rose-500 block mt-0.5">{{ form.errors.username }}</span>
    </div>

    <!-- Password Input -->
    <div class="space-y-1.5">
      <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Password</label>
      <input
        id="password"
        v-model="form.password"
        type="password"
        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-hidden focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition"
        placeholder="••••••••"
        required
      />
      <span v-if="form.errors.password" class="text-xs font-semibold text-rose-500 block mt-0.5">{{ form.errors.password }}</span>
    </div>

    <!-- Submit Action Button -->
    <button
      type="submit"
      :disabled="form.processing"
      class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold rounded-xl text-sm shadow-md shadow-blue-500/10 transition flex items-center justify-center space-x-2 cursor-pointer"
    >
      <span v-if="form.processing" class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
      <span>Sign In to Corporate Dashboard</span>
    </button>
  </form>

  <!-- Alternative Portal Gateway Links -->
  <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs gap-2 font-medium">
    <span class="text-slate-400">Polibatam Student?</span>
    <Link :href="route('login.student')" class="text-blue-600 hover:underline font-bold">&larr; Sign In via Campus SSO</Link>
  </div>
</template>

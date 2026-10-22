<!-- resources/js/Layouts/AuthLayout.vue -->
<script setup>
import { ref, watch } from 'vue';
import { Link, usePage, Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const page = usePage();

// Global Flash Error Observer for Authentication Barriers
watch(
  () => page.props.flash?.error,
  (newError) => {
    if (newError) {
      Swal.fire({
        icon: 'error',
        title: 'Authentication Failed',
        text: newError,
        confirmButtonText: 'Try Again',
        buttonsStyling: false,
        customClass: {
          confirmButton: 'px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition shadow-md shadow-rose-500/10 cursor-pointer',
          popup: 'rounded-3xl p-6 border border-slate-100 font-sans tracking-tight'
        }
      });
    }
  },
  { immediate: true }
);

// Unified System Notification Dispatches (Toasts & Modals)
watch(
  () => page.props.flash_message,
  (flash) => {
    if (!flash || !flash.message) return;

    // 1. CORNER SCREEN MINI TOAST RENDERING
    if (flash.display === 'toast') {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        background: '#ffffff',
        color: '#0f172a',
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      });

      Toast.fire({
        icon: flash.type || 'success',
        title: flash.message
      });
    }

    // 2. INDUSTRIAL DIALOG MODAL RENDERING
    else if (flash.display === 'modal') {
      Swal.fire({
        title: flash.title || 'Information Notice',
        text: flash.message,
        icon: flash.type || 'info',
        confirmButtonColor: '#2563eb',
        background: '#ffffff',
        color: '#0f172a',
      });
    }
  },
  { immediate: true, deep: true }
);
</script>

<template>
  <Head :title="page.props.title ? `${page.props.title} - Polibatam TalentHub` : 'Polibatam TalentHub'" />

  <div class="min-h-screen w-full flex bg-slate-50 text-slate-900 font-sans tracking-tight selection:bg-blue-600 selection:text-white">

    <!-- LEFT SIDE: DYNAMIC VISUAL BANNER (Hidden on Mobile) -->
    <div class="hidden lg:flex w-1/2 bg-linear-to-tr from-slate-950 via-slate-900 to-blue-950 relative p-12 flex-col justify-between overflow-hidden select-none">
      <!-- Ambient light effect backdrops -->
      <div class="absolute -top-20 -left-20 w-80 h-80 bg-blue-500/10 blur-3xl rounded-full"></div>
      <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-indigo-500/10 blur-3xl rounded-full"></div>

      <!-- Top Branding Crest -->
      <div class="flex items-center space-x-2.5 z-10">
        <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-black text-xs shadow-md shadow-blue-600/20">TH</div>
        <span class="text-white font-black text-lg tracking-wider">TalentHub.</span>
      </div>

      <!-- Center Informational Catchphrase -->
      <div class="space-y-4 max-w-md z-10">
        <h1 class="text-4xl font-black text-white leading-tight">
          Join Our Professional Community.
        </h1>
        <p class="text-slate-400 font-medium text-sm leading-relaxed">
          The ultimate gateway mapping student portfolio milestones, verified Project-Based Learning competency frameworks, and direct employment funnels straight to strategic global industries.
        </p>
      </div>

      <!-- Bottom Frame Footer Notice -->
      <div class="text-xs font-semibold text-slate-500 z-10">
        &copy; 2026 Politeknik Negeri Batam. All Rights Reserved.
      </div>
    </div>

    <!-- RIGHT SIDE: ACTIVE INPUT DISPATCH WORKSPACE -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-white">
      <div class="w-full max-w-md space-y-8">
        <slot />
      </div>
    </div>

  </div>
</template>

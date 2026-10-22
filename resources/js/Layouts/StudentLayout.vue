<!-- resources/js/Layouts/StudentLayout.vue -->
<script setup>
import { ref, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const page = usePage();

const student = page.props.student || {};
const profile = page.props.profile || {};

// Layout interaction state triggers
const isSidebarOpen = ref(true);         // Desktop toggle state
const isMobileSidebarOpen = ref(false);  // Mobile drawer state

// Unified Notification System Observers
watch(
    () => page.props.flash_message,
    (flash) => {
        if (!flash || !flash.message) return;

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

// Application Logout Workflow Handling
const handleLogout = () => {
  Swal.fire({
    title: 'Sign Out of TalentHub?',
    text: 'Your current portfolio editing workspace session will be safely terminated. Please ensure all modifications have been submitted.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Sign Out',
    cancelButtonText: 'Cancel',
    buttonsStyling: false,
    customClass: {
      confirmButton: 'px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs mr-3 transition cursor-pointer shadow-xs',
      cancelButton: 'px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition cursor-pointer border border-slate-200/40',
      popup: 'rounded-3xl p-6 font-sans tracking-tight border border-slate-100'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('logout'));
    }
  });
};

const getInitials = (name) => {
  if (!name) return 'ST';
  const words = name.trim().split(' ');
  return words.length === 1 ? words[0].substring(0, 2).toUpperCase() : (words[0][0] + words[1][0]).toUpperCase();
};

const avatarError = ref(false);
const hasValidAvatar = () => {
  if (avatarError.value) return false;
  return profile?.profile_picture && profile.profile_picture !== 'default.jpg' && profile.profile_picture !== '-';
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-900 font-sans flex flex-col lg:flex-row tracking-tight relative selection:bg-blue-600 selection:text-white">

    <!-- OVERLAY BACKGROUND (Mobile Drawer Backdrop) -->
    <div
      v-if="isMobileSidebarOpen"
      @click="isMobileSidebarOpen = false"
      class="fixed inset-0 bg-slate-950/40 z-40 lg:hidden transition-opacity duration-300"
    ></div>

    <!-- MAIN SIDEBAR NAVIGATION SYSTEM -->
    <aside
      :class="[
        isSidebarOpen ? 'lg:w-72' : 'lg:w-20',
        isMobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        'fixed lg:sticky top-0 left-0 h-screen z-50 lg:z-40 bg-slate-950 text-white flex flex-col justify-between transition-all duration-300 border-r border-slate-900 shrink-0'
      ]"
    >
      <div class="flex flex-col overflow-y-auto grow custom-scrollbar">

        <!-- ========================================================= -->
        <!-- SIDEBAR BRANDING HEADER AREA (TEXT VS SHORT ICON LOGO)    -->
        <!-- ========================================================= -->
        <div class="h-20 px-4 flex items-center justify-between border-b border-slate-900 shrink-0 bg-slate-950 relative w-full gap-2">

          <!-- Wrapper Logo/Text Dynamic Workspace -->
          <div class="flex items-center min-w-0 grow justify-start">
            <!-- Kondisi A: Saat Sidebar Lebar / Terbuka -> Tampilkan Teks Besar Warna Putih -->
            <div v-if="isSidebarOpen || isMobileSidebarOpen" class="flex items-center pl-2 select-none animate-fadeIn">
              <span class="text-lg font-black text-white tracking-wider uppercase">
                Polibatam Talent Hub
              </span>
            </div>

            <!-- Kondisi B: Saat Sidebar Ciut / Tertutup -> Tampilkan Ikon Gambar Short setinggi 45px -->
            <div v-else class="h-full w-[45px] flex items-center justify-center animate-fadeIn">
              <!-- <img src="/img/logo_talenthub_short.png" class="h-full w-[45px]" alt="Logo Ikon"> -->
            </div>
          </div>

          <!-- Desktop Hamburger Toggle Interface Menu Control Buttons -->
          <button
            @click="isSidebarOpen = !isSidebarOpen"
            class="text-slate-400 hover:text-blue-400 transition cursor-pointer hidden lg:flex p-2 hover:bg-slate-900 rounded-xl focus:outline-hidden shrink-0 items-center justify-center"
          >
            <svg v-if="isSidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h8m-8 6h16" /></svg>
          </button>

          <!-- Mobile Close Navigation Drawer Panel Triggers -->
          <button
            @click="isMobileSidebarOpen = false"
            class="text-slate-400 hover:text-blue-400 transition cursor-pointer lg:hidden p-2 hover:bg-slate-900 rounded-xl focus:outline-hidden shrink-0 flex items-center justify-center"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Student Information Mini Brief Profile -->
        <div v-if="isSidebarOpen || isMobileSidebarOpen" class="p-5 bg-slate-900/40 border-b border-slate-900/60 space-y-3 shrink-0">
          <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-xl overflow-hidden shrink-0 shadow-inner bg-slate-800 border border-slate-700/50">
              <img v-if="hasValidAvatar()" :src="`/storage/profile_picture/${profile.profile_picture}`" @error="avatarError = true" class="object-cover w-full h-full" alt="Avatar">
              <div v-else class="w-full h-full bg-linear-to-tr from-blue-600 to-indigo-700 flex items-center justify-center text-white text-xs font-black select-none">{{ getInitials(student?.name) }}</div>
            </div>
            <div class="truncate">
              <h4 class="text-xs font-bold text-slate-100 truncate" :title="student?.name">{{ student?.name ?? 'Talent Student' }}</h4>
              <p class="text-[10px] text-slate-400 font-medium truncate">NIM: {{ student?.id_mhs ?? '-' }}</p>
            </div>
          </div>
          <div class="flex items-center justify-between text-[11px] bg-slate-950 p-2 rounded-lg border border-slate-900 font-bold text-slate-400">
            <span>👁️ Profile Views:</span>
            <span class="text-blue-400">{{ profile?.views_count ?? 0 }}x</span>
          </div>
        </div>

        <!-- Dynamic Menu List Tracks -->
        <nav class="p-4 space-y-5 flex-grow">

          <!-- GROUP 1: DASHBOARD WORKSPACE -->
          <div class="space-y-1">
            <span v-if="isSidebarOpen || isMobileSidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1.5">Dashboard</span>
            <Link
              :href="route('dashboard')"
              :class="[
                route().current('dashboard')
                  ? 'bg-linear-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/10'
                  : 'text-slate-400 hover:bg-slate-900 hover:text-blue-400',
                'flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition'
              ]"
            >
              <span>🖥️</span> <span v-if="isSidebarOpen || isMobileSidebarOpen">Home Profile</span>
            </Link>
          </div>

          <!-- GROUP 2: COMPETENCIES & EXPERIENCES -->
          <div class="space-y-1">
            <span v-if="isSidebarOpen || isMobileSidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1.5">Competency</span>
            <Link
              :href="route('experiences.index')"
              :class="[
                route().current('experiences.index')
                  ? 'bg-linear-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/10'
                  : 'text-slate-400 hover:bg-slate-900 hover:text-blue-400',
                'flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition'
              ]"
            >
              <span>💼</span> <span v-if="isSidebarOpen || isMobileSidebarOpen">Professional Experiences</span>
            </Link>
          </div>

          <!-- GROUP 3: ACADEMICS TRACK -->
          <div class="space-y-1">
            <span v-if="isSidebarOpen || isMobileSidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1.5">Academic</span>
            <Link
              :href="route('education.index')"
              :class="[
                route().current('education.index')
                  ? 'bg-linear-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/10'
                  : 'text-slate-400 hover:bg-slate-900 hover:text-blue-400',
                'flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition'
              ]"
            >
              <span>🎓</span> <span v-if="isSidebarOpen || isMobileSidebarOpen">Education History</span>
            </Link>
            <Link
              :href="route('certifications.index')"
              :class="[
                route().current('certifications.index')
                  ? 'bg-linear-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/10'
                  : 'text-slate-400 hover:bg-slate-900 hover:text-blue-400',
                'flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition'
              ]"
            >
              <span>📜</span> <span v-if="isSidebarOpen || isMobileSidebarOpen">Certifications & Training</span>
            </Link>
            <Link
              :href="route('achievement.index')"
              :class="[
                route().current('achievement.index')
                  ? 'bg-linear-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/10'
                  : 'text-slate-400 hover:bg-slate-900 hover:text-blue-400',
                'flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition'
              ]"
            >
              <span>🏆</span> <span v-if="isSidebarOpen || isMobileSidebarOpen">Student Achievements</span>
            </Link>
          </div>

          <!-- GROUP 4: RELATED PRODUCTS / DIRECT PORTFOLIO -->
          <div class="space-y-1">
            <span v-if="isSidebarOpen || isMobileSidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1.5">Related Product</span>
            <Link
              :href="route('products.index')"
              :class="[
                route().current('products.index')
                  ? 'bg-linear-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/10'
                  : 'text-slate-400 hover:bg-slate-900 hover:text-blue-400',
                'flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition'
              ]"
            >
              <span>💿</span> <span v-if="isSidebarOpen || isMobileSidebarOpen">Project Portfolios</span>
            </Link>
          </div>

          <!-- GROUP 5: LANGUAGES COMPLIANCE -->
          <div class="space-y-1">
            <span v-if="isSidebarOpen || isMobileSidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1.5">Language</span>
            <Link
              :href="route('languages.index')"
              :class="[
                route().current('languages.index')
                  ? 'bg-linear-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/10'
                  : 'text-slate-400 hover:bg-slate-900 hover:text-blue-400',
                'flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition'
              ]"
            >
              <span>📖</span> <span v-if="isSidebarOpen || isMobileSidebarOpen">Language Proficiencies</span>
            </Link>
          </div>

          <!-- GROUP 6: MISCELLANEOUS OTHERS -->
          <div class="space-y-1">
            <span v-if="isSidebarOpen || isMobileSidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1.5">Others</span>
            <Link
              :href="route('references.index')"
              :class="[
                route().current('references.index')
                  ? 'bg-linear-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/10'
                  : 'text-slate-400 hover:bg-slate-900 hover:text-blue-400',
                'flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition'
              ]"
            >
              <span>📊</span> <span v-if="isSidebarOpen || isMobileSidebarOpen">Reference Ledger</span>
            </Link>
          </div>

        </nav>
      </div>

      <!-- Application Terminate Dispatch Session -->
      <div class="p-4 border-t border-slate-900 bg-slate-950 shrink-0">
        <button @click="handleLogout" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold text-rose-400 hover:bg-rose-950/40 hover:text-rose-300 transition text-left cursor-pointer focus:outline-hidden">
          <span>🚪</span> <span v-if="isSidebarOpen || isMobileSidebarOpen">Sign Out Session</span>
        </button>
      </div>
    </aside>

    <!-- RIGHT SIDE: CORE LAYOUT WORKSPACE INNER FRAME -->
    <div class="flex-grow flex flex-col min-w-0 w-full">
      <!-- Topbar Frame Header -->
      <header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 shrink-0">
        <div class="flex items-center space-x-3">
          <!-- Mobile Hamburger Drawer Trigger Button -->
          <button
            @click="isMobileSidebarOpen = true"
            class="text-slate-600 hover:text-slate-900 transition cursor-pointer lg:hidden p-1.5 hover:bg-slate-100 rounded-lg focus:outline-hidden"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
          </button>

          <h2 class="text-xs sm:text-sm font-bold text-slate-800 truncate">
            Department Track: <span class="text-blue-600 font-extrabold">{{ student?.prodi ?? '-' }}</span>
          </h2>
        </div>

        <div>
          <a :href="`/students/${student?.id_mhs}`" target="_blank" class="px-3 py-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-[11px] font-black rounded-xl transition cursor-pointer flex items-center whitespace-nowrap uppercase tracking-wider shadow-xs">
            👁️ <span class="hidden sm:inline ml-1">Live Preview </span> Public CV
          </a>
        </div>
      </header>

      <!-- Active view sub-component workspace injection slot -->
      <main class="flex-grow overflow-y-auto w-full">
        <slot />
      </main>
    </div>

  </div>
</template>

<style scoped>
/* Clean vertical scrollbar layout adjustments */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #090d16;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #1e293b;
  border-radius: 10px;
}
</style>

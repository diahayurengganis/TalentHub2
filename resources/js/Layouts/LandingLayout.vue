<!-- resources/js/Layouts/LandingLayout.vue -->
<script setup>
import { ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const isMobileMenuOpen = ref(false);
const isTalentDropdownOpen = ref(false);
const isLoginDropdownOpen = ref(false);
const isMobileLoginDropdownOpen = ref(false);

const page = usePage();

// Integrated Notification System (English Toast & Modal Support)
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
        title: flash.title || 'Information',
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
  <div class="min-h-screen flex flex-col bg-slate-50/50 text-slate-900 antialiased font-sans w-full overflow-x-hidden selection:bg-blue-600 selection:text-white">

    <!-- ======= STICKY NAVBAR (PSTEAM HEADER STRUCTURE IDAMAN) ======= -->
    <nav class="bg-white border-b border-slate-200/60 sticky top-0 z-50 shadow-xs tracking-tight">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">

          <!-- KIRI: BRANDING LOGO DENGAN STRUKTUR DOUBLE-ROW PSTEAM -->
          <div class="flex-shrink-0 flex items-center">
            <Link :href="route('landing')" class="flex items-center space-x-3 group py-1">
              <!-- Logo Gambar Utama -->
              <img
                src="/img/logo_talenthub_color.png"
                class="h-11 w-auto object-contain group-hover:scale-101 transition-transform duration-300"
                alt="Polibatam TalentHub"
              >
              <!-- Pembatas Garis Vertikal Internal -->
              <div class="hidden sm:block h-8 w-[1px] bg-slate-200"></div>
              <!-- Sub-konteks Teks Pendamping Instansi -->
              <div class="hidden sm:block text-left">
                <span class="text-[9px] font-black text-slate-400 block tracking-widest uppercase leading-none">Strategic Career Gateway</span>
                <span class="text-xs font-bold text-slate-500 block mt-0.5 leading-none">Politeknik Negeri Batam</span>
              </div>
            </Link>
          </div>

          <!-- KANAN: MENU NAVIGASI UTAMA (DESKTOP) -->
          <div class="hidden md:flex items-center gap-2">
            <Link :href="route('landing')" class="px-3 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 rounded-xl hover:bg-slate-50 transition">Home</Link>
            <a href="#faq" class="px-3 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 rounded-xl hover:bg-slate-50 transition">F.A.Q</a>

            <!-- Dropdown Talents (Interactive Trigger) -->
            <div class="relative">
              <button @click="isTalentDropdownOpen = !isTalentDropdownOpen" class="px-3 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 rounded-xl hover:bg-slate-50 transition flex items-center gap-1 cursor-pointer focus:outline-hidden">
                <span>Talents</span>
                <svg class="w-3.5 h-3.5 opacity-60 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div v-if="isTalentDropdownOpen" class="absolute right-0 mt-2 w-48 bg-white border border-slate-200/60 rounded-2xl shadow-xl py-2 z-50 animate-fadeIn">
                <Link :href="route('students.index')" @click="isTalentDropdownOpen = false" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                  Search Students
                </Link>
                <!-- <Link href="#" @click="isTalentDropdownOpen = false" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                  Internships & Jobs
                </Link> -->
              </div>
            </div>

            <!-- Vertical Separator line -->
            <div class="h-4 w-[1px] bg-slate-200 mx-2"></div>

            <!-- Portal Authentication Action Button -->
            <div class="relative">
              <button @click="isLoginDropdownOpen = !isLoginDropdownOpen" class="px-4 py-2 text-xs font-black text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md shadow-blue-600/10 transition cursor-pointer flex items-center gap-1 focus:outline-hidden uppercase tracking-wider">
                <span>Enter Portal</span>
                <svg class="w-3 h-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div v-if="isLoginDropdownOpen" class="absolute right-0 mt-2 w-60 bg-white border border-slate-200/60 rounded-2xl shadow-xl py-2 z-50 animate-fadeIn">
                <Link :href="route('login.student')" @click="isLoginDropdownOpen = false" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                  Student Login (SSO)
                </Link>
                <div class="border-t border-slate-100 my-1"></div>
                <Link :href="route('login.industry')" @click="isLoginDropdownOpen = false" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                  Industrial Partner Login
                </Link>
              </div>
            </div>
          </div>

          <!-- Hamburger & Toggle Button (VERSION MOBILE) -->
          <div class="flex md:hidden items-center">
            <button
              @click="isMobileMenuOpen = !isMobileMenuOpen"
              type="button"
              class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:bg-slate-100 focus:outline-hidden transition"
            >
              <svg v-if="!isMobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

        </div>
      </div>

      <!-- PANEL MENU MOBILE DRAWER (RESPONSIVE ALIGNED) -->
      <div v-show="isMobileMenuOpen" class="md:hidden bg-white border-t border-slate-200/60 transition-all duration-200 shadow-lg">
        <div class="px-4 pt-2 pb-4 space-y-1.5 flex flex-col text-left">
          <Link @click="isMobileMenuOpen = false" :href="route('landing')" class="px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-xl transition">Home</Link>
          <a @click="isMobileMenuOpen = false" href="#faq" class="px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-xl transition">F.A.Q</a>
          <Link @click="isMobileMenuOpen = false" :href="route('students.index')" class="px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-xl transition">Search Students</Link>

          <div class="border-t border-slate-100 my-1"></div>

          <!-- Accordion Menu Masuk Portal di Mobile -->
          <button @click="isMobileLoginDropdownOpen = !isMobileLoginDropdownOpen" class="w-full text-left flex items-center justify-between px-3 py-2 text-sm font-bold text-slate-900 hover:bg-slate-50 rounded-xl focus:outline-hidden">
            <span>Enter Portal Gateway</span>
            <svg class="w-4 h-4 opacity-60 transition-transform" :class="{ 'rotate-180': isMobileLoginDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <div v-show="isMobileLoginDropdownOpen" class="pl-4 space-y-1 bg-slate-50/50 rounded-xl p-2 animate-fadeIn">
            <Link @click="isMobileMenuOpen = false" :href="route('login.student')" class="block px-3 py-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition">Student Login (SSO)</Link>
            <Link @click="isMobileMenuOpen = false" :href="route('login.industry')" class="block px-3 py-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition">Industrial Partner Login</Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- ======= MAIN CONTENT WORKSPACE ======= -->
    <main class="w-full flex-1">
      <slot />
    </main>

    <!-- ======= CONTEMPORARY FOOTER FIXED ======= -->
    <footer class="w-full bg-white border-t border-slate-200 text-slate-500 mt-auto">
      <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-3 gap-12">
        <div class="space-y-4">
          <div class="flex items-center">
            <img src="/img/logo_talenthub_color.png" class="h-9 w-auto object-contain" alt="Polibatam TalentHub">
          </div>
          <p class="text-sm text-slate-500 leading-relaxed max-w-sm">
            Politeknik Negeri Batam's digital vocational career ecosystem matching real-time student competencies with global industrial demands.
          </p>
        </div>
        <div>
          <h4 class="text-slate-900 font-bold text-sm uppercase tracking-wider mb-4">Quick Navigation</h4>
          <div class="grid grid-cols-2 gap-2 text-sm font-medium">
            <a href="#" class="hover:text-blue-600 transition">Internships</a>
            <a href="#" class="hover:text-blue-600 transition">Job Search</a>
            <a href="#" class="hover:text-blue-600 transition">Portfolios</a>
            <a href="#" class="hover:text-blue-600 transition">Partnership</a>
          </div>
        </div>
        <div>
          <h4 class="text-slate-900 font-bold text-sm uppercase tracking-wider mb-4">Industrial Relations</h4>
          <p class="text-sm leading-relaxed">
            Politeknik Negeri Batam Campus Area<br>
            Batam Centre, Riau Islands, Indonesia<br>
            <span class="block mt-2 font-semibold text-slate-800">Email: talenthub@polibatam.ac.id</span>
          </p>
        </div>
      </div>
      <div class="w-full border-t border-slate-100 py-6 text-center text-xs text-slate-400 font-medium">
        &copy; 2026 Politeknik Negeri Batam. Crafted for better vocational connection.
      </div>
    </footer>

  </div>
</template>

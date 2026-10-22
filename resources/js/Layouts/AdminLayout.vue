<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
const page = usePage();

watch(
    () => page.props.flash_message,
    (flash) => {
        if (!flash || !flash.message) return;

        // 1. OPSI RENDERING SEBAGAI TOAST KECIL DI POJOK LAYAR
        if (flash.display === 'toast') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: flash.type || 'success', // dinamis: bisa success, error, info, atau warning
                title: flash.message
            });
        }

        // 2. OPSI RENDERING SEBAGAI MODAL BESAR
        else if (flash.display === 'modal') {
            Swal.fire({
                title: flash.title || 'Informasi',
                text: flash.message,
                icon: flash.type || 'info', // dinamis mengikuti server
                confirmButtonColor: '#1e3a8a',
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
            });
        }
    },
    { immediate: true, deep: true }
);

</script>

<template>
  <div class="min-h-screen bg-slate-100 flex font-sans">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-400 flex flex-col fixed inset-y-0 left-0 z-30">
      <!-- Sidebar Header / Logo -->
      <div class="h-16 px-6 flex items-center border-b border-slate-800">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
            <i class="fa-solid fa-cubes text-white text-md"></i>
          </div>
          <span class="text-lg font-bold text-white tracking-tight">Talenthub <span class="text-indigo-400 text-xs uppercase font-semibold">Admin</span></span>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto">
        <Link href="/admin" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-white bg-indigo-600/10 border-l-4 border-indigo-500 transition">
          <i class="fa-solid fa-chart-pie"></i> Ikhtisar Dasbor
        </Link>
        <Link href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-slate-800 hover:text-white transition">
          <i class="fa-solid fa-users"></i> Verifikasi Mahasiswa
        </Link>
        <Link href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-slate-800 hover:text-white transition">
          <i class="fa-solid fa-building"></i> Manajemen Mitra
        </Link>
        <Link href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-slate-800 hover:text-white transition">
          <i class="fa-solid fa-briefcase"></i> Verifikasi Lowongan
        </Link>
      </nav>

      <!-- Sidebar Footer -->
      <div class="p-4 border-t border-slate-800">
        <Link href="/logout" method="post" as="button" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-rose-500/10 hover:text-rose-400 text-left transition">
          <i class="fa-solid fa-power-off"></i> Keluar Sesi
        </Link>
      </div>
    </aside>

    <!-- Main Workspace Section -->
    <div class="pl-64 flex flex-col flex-grow">
      <!-- Navbar Atas Admin -->
      <header class="h-16 bg-white border-b border-slate-200 sticky top-0 px-8 flex items-center justify-between z-20">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-calendar-day text-slate-400"></i>
          <span class="text-sm font-medium text-slate-500">Dasbor Administrator</span>
        </div>

        <!-- User Avatar & Quick Info -->
        <div class="flex items-center gap-3">
          <div class="text-right">
            <h5 class="text-sm font-bold text-slate-900 leading-tight">Yudi Handoko</h5>
            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Super Administrator</span>
          </div>
          <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden border border-slate-300">
            <img src="https://placehold.co/100x100" alt="Admin Profile" class="object-cover w-full h-full">
          </div>
        </div>
      </header>

      <!-- Panel Konten Utama -->
      <main class="p-8 flex-grow">
        <slot />
      </main>
    </div>
  </div>
</template>

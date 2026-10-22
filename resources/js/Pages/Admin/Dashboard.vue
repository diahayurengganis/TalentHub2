<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });
</script>

<template>
  <Head title="Manajemen Dasbor Admin" />

  <!-- Page Title & Header -->
  <div class="flex justify-between items-center mb-8">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Ikhtisar Panel</h1>
      <p class="text-sm text-slate-500 mt-1">Selamat datang kembali! Berikut statistik analitik portal hari ini.</p>
    </div>
    <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow shadow-indigo-500/10 transition">
      <i class="fa-solid fa-download mr-1.5"></i> Ekspor Laporan
    </button>
  </div>

  <!-- Cards Grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Mahasiswa</span>
        <h3 class="text-3xl font-extrabold text-slate-900 mt-2">1,240</h3>
        <p class="text-xs text-emerald-600 font-semibold mt-1"><i class="fa-solid fa-arrow-up"></i> +12% dari minggu lalu</p>
      </div>
      <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl"><i class="fa-solid fa-user-graduate"></i></div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Perusahaan Mitra</span>
        <h3 class="text-3xl font-extrabold text-slate-900 mt-2">84</h3>
        <p class="text-xs text-emerald-600 font-semibold mt-1"><i class="fa-solid fa-arrow-up"></i> +4 Mitra Baru</p>
      </div>
      <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl"><i class="fa-solid fa-building"></i></div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Lowongan Aktif</span>
        <h3 class="text-3xl font-extrabold text-slate-900 mt-2">156</h3>
        <p class="text-xs text-amber-500 font-semibold mt-1"><i class="fa-solid fa-arrows-spin"></i> 12 Perlu Verifikasi</p>
      </div>
      <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl"><i class="fa-solid fa-briefcase"></i></div>
    </div>
  </div>

  <!-- Mock Table Data -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
      <h3 class="font-bold text-slate-900 text-base">Verifikasi Pendaftaran Mahasiswa</h3>
      <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 border border-amber-100">Menunggu Verifikasi</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-400 font-bold text-xs uppercase tracking-wider">
            <th class="px-6 py-4">Nama Lengkap</th>
            <th class="px-6 py-4">Program Studi</th>
            <th class="px-6 py-4">Nilai Kelayakan CV</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
          <tr>
            <td class="px-6 py-4 font-bold text-slate-900">Muhammad Al-Fatih</td>
            <td class="px-6 py-4 text-slate-500">Teknik Informatika</td>
            <td class="px-6 py-4"><span class="font-bold text-indigo-600">88% (Sangat Baik)</span></td>
            <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">Menunggu</span></td>
            <td class="px-6 py-4 text-right">
              <button class="text-xs bg-emerald-50 text-emerald-600 hover:bg-emerald-100 px-3 py-1.5 rounded-lg mr-2 font-bold transition">Terima</button>
              <button class="text-xs bg-rose-50 text-rose-600 hover:bg-rose-100 px-3 py-1.5 rounded-lg font-bold transition">Tolak</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

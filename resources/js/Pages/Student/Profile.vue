<script setup>
import { Head } from '@inertiajs/vue3';
import StudentLayout from '../../Layouts/StudentLayout.vue';

defineOptions({ layout: StudentLayout });
</script>

<template>
  <Head title="Kelola Portofolio Diri & CV" />

  <!-- Navigation Tab Menu -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-2.5 flex flex-wrap gap-2">
    <button class="bg-indigo-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow shadow-indigo-500/10">Data Diri & Bio</button>
    <button class="bg-transparent hover:bg-slate-50 text-slate-600 text-sm font-bold px-5 py-2.5 rounded-xl transition">Riwayat Pendidikan</button>
    <button class="bg-transparent hover:bg-slate-50 text-slate-600 text-sm font-bold px-5 py-2.5 rounded-xl transition">Pengalaman Kerja</button>
    <button class="bg-transparent hover:bg-slate-50 text-slate-600 text-sm font-bold px-5 py-2.5 rounded-xl transition">Kompetensi & Skill</button>
  </div>

  <!-- Main Profile Card Edit Form -->
  <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
    <div class="border-b border-slate-100 pb-4">
      <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Data Diri Utama</h2>
      <p class="text-sm text-slate-400 mt-1">Perbarui biodata dasar dan ringkasan profesional Anda di bawah ini.</p>
    </div>

    <form @submit.prevent class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
          <input type="text" class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none transition" value="Ananda Pradhana">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Gelar Profesional Singkat</label>
          <input type="text" class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none transition" value="Junior Frontend Developer">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ringkasan Ringkas Tentang Anda</label>
        <textarea rows="4" class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none transition resize-none">Saya mahasiswa tingkat akhir yang berdedikasi tinggi di bidang rekayasa perangkat lunak web modern khususnya Vue 3 dan Laravel framework...</textarea>
      </div>

      <div class="flex justify-end pt-4 border-t border-slate-100">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-2xl shadow shadow-indigo-500/10 transition">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</template>

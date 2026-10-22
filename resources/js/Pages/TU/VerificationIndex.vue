<template>
  <AdminLayout title="Verifikasi Data Mahasiswa">
    <div class="py-6 px-4 sm:px-6 lg:px-8">
      <!-- Header Banner / Judul Halaman -->
      <div class="mb-8 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold tracking-tight">Verifikasi Data Mahasiswa (TU)</h1>
            <p class="mt-1 text-blue-100 text-sm">
              Kelola, tinjau, dan verifikasi status portofolio serta prestasi mahasiswa Polibatam TalentHub.
            </p>
          </div>
          <div class="hidden sm:block">
            <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-white/20 text-white backdrop-blur-md">
              Panel Tata Usaha
            </span>
          </div>
        </div>
      </div>

      <!-- Ringkasan Statistik Data -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Menunggu Verifikasi</p>
            <p class="text-2xl font-extrabold text-amber-500 mt-1">
              {{ studentList.filter(s => !s.status_verifikasi || s.status_verifikasi === 'Pending').length }}
            </p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
            ⏳
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui</p>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1">
              {{ studentList.filter(s => s.status_verifikasi === 'Approved').length }}
            </p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
            ✅
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Ditolak / Revisi</p>
            <p class="text-2xl font-extrabold text-rose-600 mt-1">
              {{ studentList.filter(s => s.status_verifikasi === 'Rejected').length }}
            </p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
            ❌
          </div>
        </div>
      </div>

      <!-- Tabel Daftar Mahasiswa -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
          <h2 class="text-base font-bold text-gray-800">Daftar Pengajuan Mahasiswa</h2>
          <span class="text-xs font-medium text-gray-500">Total: {{ studentList.length }} Data</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/80 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <th class="px-6 py-4">Nama Mahasiswa</th>
                <th class="px-6 py-4">NIM</th>
                <th class="px-6 py-4">Status Verifikasi</th>
                <th class="px-6 py-4 text-center">Aksi / Tindakan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
              <tr v-for="student in studentList" :key="student.id" class="hover:bg-blue-50/30 transition">
                <td class="px-6 py-4 font-semibold text-gray-800">
                  {{ student.name || 'Mahasiswa Polibatam' }}
                </td>
                <td class="px-6 py-4 text-gray-500">
                  {{ student.nim || '-' }}
                </td>
                <td class="px-6 py-4">
                  <span 
                    :class="{
                      'bg-amber-50 text-amber-700 border-amber-200': student.status_verifikasi === 'Pending' || !student.status_verifikasi,
                      'bg-emerald-50 text-emerald-700 border-emerald-200': student.status_verifikasi === 'Approved',
                      'bg-rose-50 text-rose-700 border-rose-200': student.status_verifikasi === 'Rejected'
                    }"
                    class="px-3 py-1 text-xs font-semibold rounded-full border inline-flex items-center gap-1.5"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="{
                      'bg-amber-500': student.status_verifikasi === 'Pending' || !student.status_verifikasi,
                      'bg-emerald-500': student.status_verifikasi === 'Approved',
                      'bg-rose-500': student.status_verifikasi === 'Rejected'
                    }"></span>
                    {{ student.status_verifikasi || 'Pending' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center space-x-2">
                  <button 
                    @click="updateStatus(student.id, 'Approved')"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-medium transition shadow-sm"
                  >
                    Setujui
                  </button>
                  <button 
                    @click="updateStatus(student.id, 'Rejected')"
                    class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-medium transition shadow-sm"
                  >
                    Tolak
                  </button>
                </td>
              </tr>

              <tr v-if="!studentList || studentList.length === 0">
                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                  <div class="max-w-xs mx-auto">
                    <p class="text-3xl mb-2">📋</p>
                    <p class="text-sm font-medium text-gray-600">Belum ada data mahasiswa</p>
                    <p class="text-xs text-gray-400 mt-1">Data yang diajukan mahasiswa akan muncul di sini untuk diverifikasi.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  students: {
    type: Array,
    default: () => []
  }
})

// Menggunakan State Lokal Reaktif (agar tampilan berubah seketika saat diklik)
const studentList = ref([...props.students])

watch(() => props.students, (newVal) => {
  studentList.value = [...newVal]
}, { deep: true })

function updateStatus(id, status) {
  if (confirm("Apakah Anda yakin ingin mengubah status verifikasi?")) {
    // 1. Ubah nilai status di tampilan frontend langsung (Instan)
    const targetStudent = studentList.value.find(s => s.id === id)
    if (targetStudent) {
      targetStudent.status_verifikasi = status
    }

    // 2. Kirim request pembaruan ke Laravel Controller
    router.put("/dashboard/tu/verifications/" + id, {
      status_verifikasi: status
    }, {
      preserveScroll: true,
      preserveState: true
    })
  }
}
</script>

<script setup>
import { computed, ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  students: {
    type: Object,
    default: () => ({
      data: [],
      links: [],
      total: 0,
      from: null,
      to: null,
    }),
  },
  stats: {
    type: Object,
    default: () => ({
      pending: 0,
      approved: 0,
      rejected: 0,
      total: 0,
    }),
  },
  filters: {
    type: Object,
    default: () => ({
      search: '',
      status: 'All',
    }),
  },
})

const searchTerm = ref(props.filters?.search || '')
const statusTerm = ref(props.filters?.status || 'All')
const processingId = ref(null)
const selectedStudent = ref(null)

const studentList = computed(() => props.students?.data ?? [])

const statsData = computed(() => ({
  pending: props.stats?.pending ?? 0,
  approved: props.stats?.approved ?? 0,
  rejected: props.stats?.rejected ?? 0,
  total: props.stats?.total ?? 0,
}))

watch(
  () => props.filters,
  (filters) => {
    searchTerm.value = filters?.search || ''
    statusTerm.value = filters?.status || 'All'
  },
  { deep: true }
)

function searchStudents() {
  router.get(
    '/dashboard/tu/verifications',
    {
      search: searchTerm.value.trim() || undefined,
      status: statusTerm.value === 'All' ? undefined : statusTerm.value,
    },
    {
      preserveScroll: true,
      replace: true,
    }
  )
}

function clearSearch() {
  searchTerm.value = ''
  statusTerm.value = 'All'

  router.get(
    '/dashboard/tu/verifications',
    {},
    {
      preserveScroll: true,
      replace: true,
    }
  )
}

function openDetail(student) {
  selectedStudent.value = { ...student }
}

function closeDetail() {
  selectedStudent.value = null
}

function updateStatus(id, status) {
  if (processingId.value !== null) return

  const action = status === 'Approved' ? 'menyetujui' : 'menolak'

  if (!confirm(`Apakah Anda yakin ingin ${action} data mahasiswa ini?`)) {
    return
  }

  processingId.value = id

  router.put(
    `/dashboard/tu/verifications/${encodeURIComponent(id)}`,
    { status_verifikasi: status },
    {
      preserveScroll: true,
      onFinish: () => {
        processingId.value = null
      },
    }
  )
}

function statusClass(status) {
  if (status === 'Approved') {
    return 'bg-emerald-50 text-emerald-700 border-emerald-200'
  }

  if (status === 'Rejected') {
    return 'bg-rose-50 text-rose-700 border-rose-200'
  }

  return 'bg-amber-50 text-amber-700 border-amber-200'
}

function statusText(status) {
  return status || 'Pending'
}
</script>

<template>
  <AdminLayout>
    <div class="min-h-screen bg-slate-100 px-4 py-8 sm:px-6 lg:px-10">
      <div class="mx-auto max-w-7xl space-y-8">

        <!-- Header -->
        <div
          class="flex flex-col justify-between gap-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 p-7 text-white shadow-lg sm:flex-row sm:items-center"
        >
          <div>
            <h1 class="text-2xl font-bold sm:text-3xl">
              Verifikasi Data Mahasiswa (TU)
            </h1>
            <p class="mt-2 text-sm text-blue-100 sm:text-base">
              Kelola, tinjau, dan verifikasi status portofolio serta prestasi mahasiswa Polibatam TalentHub.
            </p>
          </div>

          <div
            class="w-fit rounded-xl bg-white/20 px-5 py-3 text-sm font-semibold"
          >
            Panel Tata Usaha
          </div>
        </div>

        <!-- Statistik -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">MENUNGGU VERIFIKASI</p>
            <p class="mt-2 text-3xl font-bold text-amber-500">
              {{ statsData.pending }}
            </p>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">DISETUJUI</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">
              {{ statsData.approved }}
            </p>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">DITOLAK / REVISI</p>
            <p class="mt-2 text-3xl font-bold text-rose-600">
              {{ statsData.rejected }}
            </p>
          </div>
        </div>

        <!-- Daftar mahasiswa -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="p-6">
            <h2 class="text-lg font-bold text-slate-800">
              Daftar Pengajuan Mahasiswa
            </h2>

            <!-- Pencarian dan filter -->
            <form
              class="mt-5 flex flex-col gap-3 lg:flex-row"
              @submit.prevent="searchStudents"
            >
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Cari nama mahasiswa atau NIM..."
                class="min-w-0 flex-1 rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              />

              <select
                v-model="statusTerm"
                class="rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500"
              >
                <option value="All">Semua Status</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
              </select>

              <div class="flex gap-2">
                <button
                  type="submit"
                  class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                  Cari
                </button>

                <button
                  type="button"
                  @click="clearSearch"
                  class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                  Reset
                </button>
              </div>
            </form>

            <p class="mt-4 text-sm text-slate-500">
              Menampilkan {{ props.students?.from ?? 0 }}–{{ props.students?.to ?? 0 }}
              dari {{ props.students?.total ?? 0 }} mahasiswa
            </p>
          </div>

          <!-- Tabel -->
          <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] table-auto">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    Nama Mahasiswa
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    NIM
                  </th>
                  <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                    Status Verifikasi
                  </th>
                  <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                    Aksi / Tindakan
                  </th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="student in studentList"
                  :key="student.id"
                  class="transition hover:bg-slate-50"
                >
                  <td class="px-6 py-4">
                    <p class="font-semibold text-slate-800">
                      {{ student.name || '-' }}
                    </p>
                  </td>

                  <td class="px-6 py-4 text-sm text-slate-600">
                    {{ student.nim || student.id || '-' }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    <span
                      class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold"
                      :class="statusClass(student.status_verifikasi)"
                    >
                      {{ statusText(student.status_verifikasi) }}
                    </span>
                  </td>

                  <td class="px-6 py-4">
                    <div class="flex flex-wrap items-center justify-center gap-2">
                      <button
                        type="button"
                        @click.stop="openDetail(student)"
                        class="whitespace-nowrap rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
                      >
                        Lihat Detail
                      </button>

                      <button
                        type="button"
                        @click="updateStatus(student.id, 'Approved')"
                        :disabled="processingId !== null"
                        class="whitespace-nowrap rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                      >
                        {{ processingId === student.id ? 'Memproses...' : 'Setujui' }}
                      </button>

                      <button
                        type="button"
                        @click="updateStatus(student.id, 'Rejected')"
                        :disabled="processingId !== null"
                        class="whitespace-nowrap rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50"
                      >
                        {{ processingId === student.id ? 'Memproses...' : 'Tolak' }}
                      </button>
                    </div>
                  </td>
                </tr>

                <tr v-if="studentList.length === 0">
                  <td colspan="4" class="px-6 py-12 text-center">
                    <p class="font-semibold text-slate-600">
                      Data mahasiswa tidak ditemukan.
                    </p>
                    <p class="mt-1 text-sm text-slate-400">
                      Coba gunakan kata kunci atau filter status yang berbeda.
                    </p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div
            v-if="props.students?.links?.length > 0"
            class="flex flex-col gap-4 border-t border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
          >
            <p class="text-sm text-slate-500">
              Halaman data mahasiswa
            </p>

            <div class="flex flex-wrap items-center gap-1">
              <template
                v-for="(link, index) in props.students.links"
                :key="index"
              >
                <Link
                  v-if="link.url"
                  :href="link.url"
                  preserve-scroll
                  class="rounded-lg border px-3 py-2 text-sm transition"
                  :class="link.active
                    ? 'border-blue-600 bg-blue-600 font-semibold text-white'
                    : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                  v-html="link.label"
                />

                <span
                  v-else
                  class="cursor-not-allowed rounded-lg border border-slate-100 px-3 py-2 text-sm text-slate-300"
                  v-html="link.label"
                />
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal detail mahasiswa: berada di luar tabel -->
    <Teleport to="body">
      <div
        v-if="selectedStudent"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/60 p-4"
        @click.self="closeDetail"
        @keydown.esc="closeDetail"
      >
        <div
          class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
          role="dialog"
          aria-modal="true"
          aria-labelledby="student-detail-title"
        >
          <!-- Header modal -->
          <div class="flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-700 p-5 text-white">
            <div>
              <h2 id="student-detail-title" class="text-xl font-bold">
                Detail Mahasiswa
              </h2>
              <p class="mt-1 text-sm text-blue-100">
                Informasi profil TalentHub
              </p>
            </div>

            <button
              type="button"
              @click="closeDetail"
              aria-label="Tutup detail"
              class="rounded-lg px-3 py-1 text-3xl leading-none transition hover:bg-white/20"
            >
              &times;
            </button>
          </div>

          <div class="space-y-6 p-6">
            <!-- Identitas -->
            <div class="flex items-center gap-4">
              <img
                v-if="selectedStudent.profile_picture"
                :src="selectedStudent.profile_picture"
                alt="Foto profil mahasiswa"
                class="h-20 w-20 rounded-full border border-slate-200 object-cover"
                @error="$event.target.style.display = 'none'"
              />

              <div
                v-else
                class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-blue-50 text-2xl font-bold text-blue-700"
              >
                {{ (selectedStudent.name || 'M').charAt(0).toUpperCase() }}
              </div>

              <div class="min-w-0">
                <h3 class="break-words text-lg font-bold text-slate-800">
                  {{ selectedStudent.name || '-' }}
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                  NIM: {{ selectedStudent.nim || selectedStudent.id || '-' }}
                </p>
                <p class="text-sm text-slate-500">
                  {{ selectedStudent.prodi || '-' }}
                </p>

                <span
                  class="mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold"
                  :class="statusClass(selectedStudent.status_verifikasi)"
                >
                  {{ statusText(selectedStudent.status_verifikasi) }}
                </span>
              </div>
            </div>

            <!-- Data profil -->
            <div>
              <h3 class="mb-3 border-b border-slate-200 pb-2 font-bold text-slate-800">
                Informasi Akademik dan Pribadi
              </h3>

              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <p class="text-xs font-medium text-slate-500">Jurusan</p>
                  <p class="mt-1 break-words text-sm text-slate-800">
                    {{ selectedStudent.jurusan || '-' }}
                  </p>
                </div>

                <div>
                  <p class="text-xs font-medium text-slate-500">IPK</p>
                  <p class="mt-1 text-sm text-slate-800">
                    {{ selectedStudent.gpa ?? '-' }}
                  </p>
                </div>

                <div>
                  <p class="text-xs font-medium text-slate-500">Email Pribadi</p>
                  <p class="mt-1 break-words text-sm text-slate-800">
                    {{ selectedStudent.personal_email || '-' }}
                  </p>
                </div>

                <div>
                  <p class="text-xs font-medium text-slate-500">Nomor Telepon</p>
                  <p class="mt-1 break-words text-sm text-slate-800">
                    {{ selectedStudent.phone || '-' }}
                  </p>
                </div>

                <div class="sm:col-span-2">
                  <p class="text-xs font-medium text-slate-500">Alamat</p>
                  <p class="mt-1 whitespace-pre-line break-words text-sm text-slate-800">
                    {{ selectedStudent.address || '-' }}
                  </p>
                </div>

                <div class="sm:col-span-2">
                  <p class="text-xs font-medium text-slate-500">Keterampilan</p>
                  <p class="mt-1 whitespace-pre-line break-words text-sm text-slate-800">
                    {{ selectedStudent.skill || '-' }}
                  </p>
                </div>

                <div class="sm:col-span-2">
                  <p class="text-xs font-medium text-slate-500">Profil / Biografi</p>
                  <p class="mt-1 whitespace-pre-line break-words text-sm text-slate-800">
                    {{ selectedStudent.profile || '-' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Media sosial -->
            <div>
              <h3 class="mb-3 border-b border-slate-200 pb-2 font-bold text-slate-800">
                Media Sosial
              </h3>

              <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <p class="break-words text-sm text-slate-700">
                  <strong>LinkedIn:</strong> {{ selectedStudent.linkedin || '-' }}
                </p>
                <p class="break-words text-sm text-slate-700">
                  <strong>Instagram:</strong> {{ selectedStudent.instagram || '-' }}
                </p>
                <p class="break-words text-sm text-slate-700">
                  <strong>Twitter:</strong> {{ selectedStudent.twitter || '-' }}
                </p>
                <p class="break-words text-sm text-slate-700">
                  <strong>Facebook:</strong> {{ selectedStudent.facebook || '-' }}
                </p>
              </div>
            </div>

            <!-- Footer modal -->
            <div class="flex justify-end border-t border-slate-100 pt-4">
              <button
                type="button"
                @click="closeDetail"
                class="rounded-lg bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

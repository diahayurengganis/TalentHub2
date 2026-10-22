<!-- resources/js/Pages/Landing/SearchStudent.vue -->
<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';

defineOptions({ layout: LandingLayout });

const props = defineProps({
  students: Object,
  prodiList: {
    type: Array,
    default: () => [] // Proteksi jika data belum dikirim dari backend
  },
  filters: Object
});

const search = ref(props.filters.search ?? '');
const selectedProdi = ref(props.filters.prodi ?? '');

// Timeout tracker untuk debounce pencarian reaktif
let timeoutId = null;

watch([search, selectedProdi], ([newSearch, newProdi]) => {
  clearTimeout(timeoutId);

  timeoutId = setTimeout(() => {
    router.get(route('students.index'), {
      search: newSearch,
      prodi: newProdi
    }, {
      preserveState: true,
      replace: true
    });
  }, 300);
});

// Helper function untuk memetakan kelompok prodi berdasarkan jurusan
const getProdiByDepartment = (departmentName) => {
  if (!props.prodiList || props.prodiList.length === 0) return [];
  return props.prodiList.filter(item => item.jurusan === departmentName);
};
</script>

<template>
  <Head title="Explore Competent Talents - Polibatam TalentHub" />

  <section class="py-12 bg-slate-50/50 min-h-screen tracking-tight">
    <div class="max-w-7xl mx-auto px-6 space-y-8">

      <!-- Title & Filter Header Bar (Bento Workspace Header) -->
      <div class="p-8 bg-white border border-slate-200/60 rounded-3xl flex flex-col lg:flex-row lg:items-center justify-between gap-6 shadow-xs">
        <div class="space-y-1">
          <div class="flex items-center space-x-2 text-blue-600">
            <span class="text-sm">🔍</span>
            <span class="text-xs font-black uppercase tracking-wider">Talent Discovery Pipeline</span>
          </div>
          <h2 class="text-2xl font-black text-slate-900 tracking-tight">Talent Directory</h2>
          <p class="text-xs text-slate-400 font-medium">Discover Politeknik Negeri Batam students based on core skill competencies and PBL achievements.</p>
        </div>

        <!-- Input Filtering Controls Area -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
          <!-- Text Box Search -->
          <div class="relative w-full sm:w-72">
            <input
              v-model="search"
              type="text"
              placeholder="Search by name, NIM or keyword..."
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:outline-hidden focus:border-blue-600 focus:ring-1 focus:ring-blue-600 bg-slate-50/30 transition placeholder:text-slate-400"
            />
          </div>

          <!-- Structured Dropdown Select Option -->
          <div class="relative w-full sm:w-64">
            <select
              v-model="selectedProdi"
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 focus:outline-hidden focus:border-blue-600 bg-white cursor-pointer"
            >
              <!-- PERBAIKAN: Opsi default dinonaktifkan (disabled) -->
              <option value="" disabled selected hidden>Choose Study Program...</option>

              <!-- Tombol Reset Filter Utama di dalam Select -->
              <option value="">Select All Programs</option>

              <!-- Render Grouping jika prodiList sudah dikirim dari backend -->
              <template v-if="props.prodiList && props.prodiList.length > 0">
                <!-- 1. Informatics Engineering Group -->
                <optgroup label="── Informatics Engineering ──" class="text-[11px] font-black text-slate-400 bg-slate-50 text-center py-1">
                  <option
                    v-for="prodi in getProdiByDepartment('Teknik Informatika')"
                    :key="prodi.prodi"
                    :value="prodi.prodi"
                    class="text-left font-semibold text-slate-700 bg-white"
                  >
                    {{ prodi.study_program }}
                  </option>
                </optgroup>

                <!-- 2. Business Management Group -->
                <optgroup label="── Business Management ──" class="text-[11px] font-black text-slate-400 bg-slate-50 text-center py-1">
                  <option
                    v-for="prodi in getProdiByDepartment('Manajemen Bisnis')"
                    :key="prodi.prodi"
                    :value="prodi.prodi"
                    class="text-left font-semibold text-slate-700 bg-white"
                  >
                    {{ prodi.study_program }}
                  </option>
                </optgroup>

                <!-- 3. Electrical Engineering Group -->
                <optgroup label="── Electrical Engineering ──" class="text-[11px] font-black text-slate-400 bg-slate-50 text-center py-1">
                  <option
                    v-for="prodi in getProdiByDepartment('Teknik Elektronika')"
                    :key="prodi.prodi"
                    :value="prodi.prodi"
                    class="text-left font-semibold text-slate-700 bg-white"
                  >
                    {{ prodi.study_program }}
                  </option>
                </optgroup>

                <!-- 4. Mechanical Engineering Group -->
                <optgroup label="── Mechanical Engineering ──" class="text-[11px] font-black text-slate-400 bg-slate-50 text-center py-1">
                  <option
                    v-for="prodi in getProdiByDepartment('Teknik Mesin')"
                    :key="prodi.prodi"
                    :value="prodi.prodi"
                    class="text-left font-semibold text-slate-700 bg-white"
                  >
                    {{ prodi.study_program }}
                  </option>
                </optgroup>
              </template>
            </select>
          </div>
        </div>
      </div>

      <!-- Bento Grid Rendering Student Cards -->
      <div v-if="students.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="item in students.data"
          :key="item.id_mhs"
          class="p-5 bg-white border border-slate-200/60 rounded-3xl hover:shadow-xl hover:shadow-slate-200/30 transition-all duration-300 flex flex-col justify-between group"
        >
          <div class="space-y-4 text-center sm:text-left">
            <!-- Profile Picture Box -->
            <div class="flex justify-center sm:justify-start">
              <img
                :src="item.profile_picture ? `/storage/profile_picture/${item.profile_picture}` : '/storage/profile_picture/default.jpg'"
                class="w-14 h-14 rounded-2xl object-cover border border-slate-100 shadow-inner bg-slate-50"
                alt="Student Avatar"
              />
            </div>

            <div class="space-y-1">
              <h4 class="font-black text-slate-900 text-sm line-clamp-1 group-hover:text-blue-600 transition-colors">{{ item.name }}</h4>
              <p class="text-[10px] font-extrabold text-blue-600 uppercase tracking-wide bg-blue-50/60 px-2 py-0.5 rounded-md inline-block">{{ item.prodi }}</p>
              <p class="text-[11px] text-slate-400 font-medium tracking-tight block pt-0.5">NIM / ID: {{ item.id_mhs }}</p>
            </div>

            <!-- Mini Skill Tags Dynamic Rendering -->
            <div v-if="item.skill" class="flex flex-wrap gap-1.5 pt-1 justify-center sm:justify-start">
              <span v-for="tag in item.skill.split(',').slice(0, 3)" :key="tag" class="px-2 py-0.5 bg-slate-100 rounded text-[9px] font-bold text-slate-500 uppercase tracking-wide">
                {{ tag.trim() }}
              </span>
            </div>
          </div>

          <!-- Action Button Grid Footer -->
          <div class="pt-4 mt-5 border-t border-slate-100/80">
            <Link
              :href="route('students.show', { id_mhs: item.id_mhs })"
              class="w-full py-2 bg-slate-50 hover:bg-blue-600 border border-slate-200/40 text-slate-700 hover:text-white rounded-xl text-[11px] font-black text-center transition block"
            >
              View CV Portfolio &rarr;
            </Link>
          </div>
        </div>
      </div>

      <!-- Empty State Handling Grid -->
      <div v-else class="text-center py-20 bg-white border border-slate-200/60 rounded-3xl space-y-2 max-w-2xl mx-auto">
        <div class="text-slate-300 font-black text-4xl select-none">🔍</div>
        <h4 class="font-black text-sm text-slate-800 uppercase tracking-wide">No Talents Found</h4>
        <p class="text-xs text-slate-400 font-medium max-w-sm mx-auto leading-relaxed">We couldn't find any candidate matching your criteria. Try adjusting your search keywords or choosing a different study program track.</p>
      </div>

      <!-- Pagination Navigation Element Links -->
<!-- Pagination Navigation Element Links (Fixed Core Integration) -->
      <div v-if="students.links.length > 3" class="flex items-center justify-center space-x-1 pt-4">
        <template v-for="link in students.links" :key="link.label">
          <!-- Gunakan Link komponen langsung, ga perlu pakai <Component :is="..."> dinamis -->
          <Link
            v-if="link.url"
            :href="link.url"
            v-html="link.label"
            class="px-3 py-1.5 rounded-xl text-xs font-bold transition block"
            :class="{
              'bg-blue-600 text-white shadow-md shadow-blue-600/10': link.active,
              'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50': !link.active
            }"
            :data="{ search: search, prodi: selectedProdi }"
            preserve-state
            preserve-scroll
          />
          <span
            v-else
            v-html="link.label"
            class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-300 pointer-events-none bg-slate-100/30 block"
          />
        </template>
      </div>

      
    </div>
  </section>
</template>

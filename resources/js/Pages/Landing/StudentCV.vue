<!-- resources/js/Pages/Landing/StudentCV.vue -->
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';

defineOptions({ layout: LandingLayout });

// Mendefinisikan properti kiriman dari controller Laravel
const props = defineProps({
  student: Object,
  profile: Object,
  academics: Array,
  achievements: Array,
  experiences: Array,
  products: Array,
  competencies: Array
});

// Helper untuk merapikan visual rentang bulan dan tahun kerja
const formatExperienceDate = (dateString, isEnd = false) => {
  if (isEnd && !dateString) return 'Present';
  if (!dateString) return '-';
  const options = { year: 'numeric', month: 'short' };
  return new Date(dateString).toLocaleDateString('en-US', options);
};

// Helper standar untuk tanggal umum (Achievement, dll)
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('en-US', options);
};
</script>

<template>
  <Head :title="`${student.name} - Digital CV & Portfolio`" />

  <section class="py-12 bg-slate-50/50 min-h-screen tracking-tight">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

      <!-- ========================================== -->
      <!-- LEFT COLUMN: BIODATA, SKILLS, & COMPETENCY -->
      <!-- ========================================== -->
      <div class="space-y-6 lg:sticky lg:top-24">

        <!-- Bento 1: Core Identity Card -->
        <div class="p-6 bg-white border border-slate-200/60 rounded-3xl shadow-xs text-center sm:text-left space-y-4">
          <div class="flex flex-col sm:flex-row items-center gap-4">
            <img
              :src="profile?.profile_picture ? `/storage/profile_picture/${profile.profile_picture}` : '/storage/profile_picture/default.jpg'"
              class="w-20 h-20 rounded-2xl object-cover border border-slate-100 bg-slate-50 shadow-inner"
              alt="Profile Picture"
            />
            <div class="space-y-1 text-center sm:text-left">
              <h1 class="text-xl font-black text-slate-900 leading-tight">{{ student.name }}</h1>
              <p class="text-xs font-bold text-blue-600 uppercase tracking-wide">{{ student.prodi }}</p>
              <p class="text-xs text-slate-400 font-medium">NIM / ID: {{ student.id_mhs }}</p>
            </div>
          </div>

          <div v-if="profile?.gpa" class="inline-flex items-center space-x-1.5 px-3 py-1 bg-green-50 border border-green-100 rounded-lg text-xs font-bold text-green-700">
            <span>📊 Cumulative GPA: {{ profile.gpa }}</span>
          </div>

          <p v-if="profile?.profile" class="text-sm text-slate-500 font-medium leading-relaxed pt-2 border-t border-slate-100 text-left">
            {{ profile.profile }}
          </p>

          <!-- Dynamic Views Count Card -->
          <div class="inline-flex items-center space-x-1.5 px-3 py-1 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-600">
            <span>👁️ Profile Views: {{ profile?.views_count ?? 0 }} times</span>
          </div>
        </div>

        <!-- Bento 2: Contact & Social Networking -->
        <div class="p-6 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-4 text-sm font-semibold text-slate-700">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Contact & Socials</h3>

          <div class="space-y-3">
            <div class="flex items-center space-x-2">
              <span class="text-slate-400">📧</span>
              <span class="truncate font-medium text-slate-600">{{ profile?.personal_email ?? '-' }}</span>
            </div>
            <div class="flex items-center space-x-2">
              <span class="text-slate-400">📞</span>
              <span class="font-medium text-slate-600">{{ profile?.phone ?? '-' }}</span>
            </div>
            <div class="flex items-center space-x-2">
              <span class="text-slate-400">📍</span>
              <span class="font-medium text-slate-600 line-clamp-1" :title="profile?.address">{{ profile?.address ?? '-' }}</span>
            </div>
          </div>

          <!-- Social Media Anchors -->
          <div v-if="profile?.linkedin || profile?.instagram || profile?.twitter" class="flex flex-wrap gap-2 pt-3 border-t border-slate-100">
            <a v-if="profile.linkedin" :href="profile.linkedin" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 rounded-xl text-xs transition">LinkedIn</a>
            <a v-if="profile.instagram" :href="profile.instagram" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-pink-50 hover:text-pink-600 rounded-xl text-xs transition">Instagram</a>
            <a v-if="profile.twitter" :href="profile.twitter" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-sky-50 hover:text-sky-600 rounded-xl text-xs transition">Twitter</a>
          </div>
        </div>

        <!-- Bento 3: Core Skills & Tech Stack -->
        <div v-if="profile?.skill" class="p-6 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-3">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Core Expertise</h3>
          <div class="flex flex-wrap gap-1.5">
            <span v-for="skill in profile.skill.split(',')" :key="skill" class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100/50 rounded-xl text-xs font-bold uppercase tracking-wide">
              {{ skill.trim() }}
            </span>
          </div>
        </div>

        <!-- Bento 4: Real-time Academic/PBL Competency Metrics -->
        <div v-if="competencies.length > 0" class="p-6 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-4">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">PBL Competency Milestones</h3>
          <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto pr-1">
            <div v-for="comp in competencies" :key="comp.id" class="py-2.5 flex items-start justify-between gap-4 text-xs font-semibold">
              <div>
                <h4 class="text-slate-800 font-bold line-clamp-1">{{ comp.nama_matkul }}</h4>
                <p class="text-[10px] text-slate-400 font-medium">{{ comp.competency }}</p>
              </div>
              <span class="px-2 py-0.5 bg-slate-900 text-white rounded-md font-black text-[10px]">{{ comp.nilai }}</span>
            </div>
          </div>
        </div>

      </div>

      <!-- ========================================== -->
      <!-- RIGHT COLUMN: PORTFOLIOS & EXPERIENCE MAPS -->
      <!-- ========================================== -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Navigation Context Top Bar -->
        <div class="flex justify-between items-center">
          <Link :href="route('students.index')" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-blue-600 group">
            <span class="transform group-hover:-translate-x-1 transition-transform mr-1">&larr;</span> Back to Directory
          </Link>
          <span class="text-xs font-bold text-slate-400 uppercase tracking-widest bg-white px-3 py-1 rounded-lg border">Digital Resume</span>
        </div>

        <!-- Block 1: Product Portfolio Exhibits (Project-Based Learning Showcase) -->
        <div class="p-6 sm:p-8 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-6">
          <h3 class="text-lg font-black text-slate-900 border-l-4 border-blue-600 pl-3">Project-Based Learning / Engineering Products</h3>
          <div v-if="products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="prod in products" :key="prod.id" class="p-5 border border-slate-100 rounded-2xl bg-slate-50/50 flex flex-col justify-between group hover:border-blue-500/30 transition">
              <div class="space-y-2">
                <h4 class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ prod.title }}</h4>
                <p class="text-xs text-slate-500 font-medium leading-relaxed line-clamp-3">{{ prod.description }}</p>
              </div>
              <a :href="prod.link" target="_blank" class="mt-4 inline-flex items-center text-xs font-bold text-blue-600 hover:underline">
                Open Product Pipeline &rarr;
              </a>
            </div>
          </div>
          <p v-else class="text-sm text-slate-400 font-medium italic">No interactive PBL product catalogs attached to this record yet.</p>
        </div>

        <!-- Block 2: Academic Projects History -->
        <div class="p-6 sm:p-8 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-6">
          <h3 class="text-lg font-black text-slate-900 border-l-4 border-indigo-600 pl-3">Academic Achievements & Course Projects</h3>
          <div v-if="academics.length > 0" class="space-y-6 relative border-l-2 border-slate-100 pl-4 ml-2">
            <div v-for="acad in academics" :key="acad.id" class="relative space-y-1.5">
              <div class="absolute -left-[21px] top-1.5 h-2 w-2 rounded-full bg-indigo-600 ring-4 ring-white"></div>
              <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md uppercase tracking-wider">{{ acad.year }}</span>
              <h4 class="font-bold text-slate-900 text-base">{{ acad.title }}</h4>
              <p class="text-xs font-bold text-slate-400">{{ acad.institution }}</p>
              <p class="text-xs text-slate-500 font-medium leading-relaxed">{{ acad.description }}</p>
            </div>
          </div>
          <p v-else class="text-sm text-slate-400 font-medium italic">No verified core course project tracks recorded.</p>
        </div>

        <!-- Block 3: Industrial Employment & Internships History (🌟 FIXED TIMELINE SECTION) -->
        <div class="p-6 sm:p-8 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-6">
          <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">Professional Experience & Internships</h3>
          <div v-if="experiences.length > 0" class="space-y-6 relative border-l-2 border-slate-100 pl-4 ml-2">
            <div v-for="exp in experiences" :key="exp.id" class="relative space-y-1.5">
              <div class="absolute -left-[21px] top-1.5 h-2 w-2 rounded-full bg-emerald-600 ring-4 ring-white"></div>
              <div class="flex items-center space-x-2">
                <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md uppercase tracking-wider border border-emerald-100">{{ exp.employment_type }}</span>
                <span class="text-[10px] text-slate-400 font-bold">
                  {{ formatExperienceDate(exp.start_date) }} - {{ formatExperienceDate(exp.end_date, true) }}
                </span>
              </div>
              <h4 class="font-bold text-slate-900 text-base">{{ exp.title }}</h4>
              <p class="text-xs font-bold text-slate-500">{{ exp.company }} &bull; <span class="text-slate-400 font-medium">{{ exp.location }}</span></p>
            </div>
          </div>
          <p v-else class="text-sm text-slate-400 font-medium italic">No corporate internship or external industrial work history logged.</p>
        </div>

        <!-- Block 4: Competitions & Honors Awards -->
        <div class="p-6 sm:p-8 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-6">
          <h3 class="text-lg font-black text-slate-900 border-l-4 border-amber-500 pl-3">Honors, Awards & Achievements</h3>
          <div v-if="achievements.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="ach in achievements" :key="ach.id" class="p-5 border border-slate-200/60 bg-white rounded-2xl space-y-3">
              <div class="flex items-center justify-between">
                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200/50 rounded text-[10px] font-bold uppercase tracking-wider">{{ ach.level }}</span>
                <span class="text-[10px] text-slate-400 font-bold">{{ formatDate(ach.time) }}</span>
              </div>
              <div>
                <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ ach.achievement }}</h4>
                <p class="text-xs text-slate-400 font-medium mt-0.5">{{ ach.organization }}</p>
                <p class="text-xs text-slate-500 font-medium leading-relaxed mt-2 line-clamp-3">{{ ach.description }}</p>
              </div>
            </div>
          </div>
          <p v-else class="text-sm text-slate-400 font-medium italic">No competitive honors or external organization awards logged.</p>
        </div>

      </div>

    </div>
  </section>
</template>

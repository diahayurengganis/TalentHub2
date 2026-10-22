<!-- resources/js/Pages/Student/Dashboard.vue -->
<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: StudentLayout });

const props = defineProps({
  student: Object,
  profile: Object,
  certifications: Array,
  achievements: Array,
  educations: Array,
  experiences: Array,
  products: Array,
  languages: Array,
  competencies: Array
});

// Reactivity for profile picture file preview manipulation
const fileInput = ref(null);
const imagePreview = ref(null);

const form = useForm({
  profile: props.profile?.profile === '-' ? '' : props.profile?.profile,
  personal_email: props.profile?.personal_email ?? '',
  phone: props.profile?.phone === '-' ? '' : props.profile?.phone,
  address: props.profile?.address === '-' ? '' : props.profile?.address,
  skill: props.profile?.skill === '-' ? '' : props.profile?.skill,
  linkedin: props.profile?.linkedin ?? '',
  instagram: props.profile?.instagram ?? '',
  twitter: props.profile?.twitter ?? '',
  profile_picture: null,
});

// Trigger click mapping straight into hidden input stream file selectors
const triggerFileSelect = () => {
  fileInput.value.click();
};

// Validasi tipe & ukuran file (< 1MB) di Frontend + Generate Preview
const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  // 1. Validasi Tipe Berkas (Mimes)
  const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
  if (!allowedTypes.includes(file.type)) {
    Swal.fire({
      title: 'Invalid File Type',
      text: 'Please select a valid image file structure (JPG, JPEG, or PNG).',
      icon: 'error',
      confirmButtonColor: '#2563eb'
    });
    e.target.value = '';
    return;
  }

  // 2. Validasi Ukuran File (Maksimal 1MB)
  const maxBytes = 1 * 1024 * 1024;
  if (file.size > maxBytes) {
    Swal.fire({
      title: 'File Too Large',
      text: 'The chosen profile image exceeds the maximum 1MB boundary size restriction.',
      icon: 'error',
      confirmButtonColor: '#2563eb'
    });
    e.target.value = '';
    return;
  }

  form.profile_picture = file;

  const reader = new FileReader();
  reader.onload = (event) => {
    imagePreview.value = event.target.result;
  };
  reader.readAsDataURL(file);
};

// Fungsi pembatalan pilihan foto baru
const cancelImageSelection = () => {
  form.profile_picture = null;
  imagePreview.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const submitUpdate = () => {
  form.post(route('profile.update'), {
    forceFormData: true,
    preserveScroll: true,
    onBefore: (request) => {
      request.data._method = 'PUT';
    },
    onSuccess: () => {
      form.profile_picture = null;
      imagePreview.value = null;
    }
  });
};

// SMART PORTFOLIO COMPLETENESS ADVISOR LOGIC
const portfolioSuggestions = computed(() => {
  const suggestions = [];

  if (!props.profile?.profile || props.profile.profile === '-') {
    suggestions.push({ id: 'bio', icon: '📝', text: 'Write a compelling short bio summary to pitch yourself to recruiters.' });
  }
  if (!props.profile?.skill || props.profile.skill === '-') {
    suggestions.push({ id: 'skills', icon: '⚡', text: 'Add your technical core competencies and tech stack framework skills.' });
  }
  if (!props.products || props.products.length === 0) {
    suggestions.push({ id: 'pbl', icon: '🚀', text: 'Link at least one Project-Based Learning artifact or software product repository.' });
  }
  if (!props.experiences || props.experiences.length === 0) {
    suggestions.push({ id: 'exp', icon: '💼', text: 'Add internal campus roles, freelance tracks, or industrial internship logs.' });
  }
  if (!props.languages || props.languages.length === 0) {
    suggestions.push({ id: 'lang', icon: '📖', text: 'Declare your linguistic language proficiencies fluencies metrics.' });
  }
  if (!props.achievements || props.achievements.length === 0) {
    suggestions.push({ id: 'ach', icon: '🏆', text: 'Log any competitive tournament honors or academic award milestones.' });
  }

  return suggestions;
});

const completenessPercentage = computed(() => {
  const totalChecks = 6;
  const missingCount = portfolioSuggestions.value.length;
  return Math.round(((totalChecks - missingCount) / totalChecks) * 100);
});
</script>

<template>
  <Head title="Portfolio Dashboard - TalentHub" />

  <div class="p-4 sm:p-8 bg-slate-50 min-h-screen tracking-tight">
    <div class="max-w-7xl mx-auto space-y-6">

      <!-- ========================================== -->
      <!-- DYNAMIC TALENT WELCOME CORE HERO BANNER    -->
      <!-- ========================================== -->
      <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 text-white p-6 sm:p-8 rounded-3xl border border-blue-900/30 relative overflow-hidden shadow-xs">
        <div class="absolute top-0 right-0 w-80 h-full bg-radial from-blue-500/10 to-transparent blur-2xl"></div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
          <div class="space-y-1.5">
            <span class="text-[10px] font-black text-blue-400 bg-blue-950/60 px-2.5 py-1 rounded-md uppercase tracking-wider">Student Console</span>
            <h1 class="text-xl sm:text-2xl font-black">{{ student.name }}</h1>
            <p class="text-xs text-slate-400 max-w-xl font-medium">Student ID / NIM: {{ student.id_mhs }} <span class="text-slate-600 mx-1">&bull;</span> {{ student.prodi }}</p>
          </div>
          <div class="bg-slate-900/60 border border-slate-800 px-5 py-2.5 rounded-2xl text-center shrink-0">
            <span class="text-[10px] font-bold text-slate-500 block uppercase tracking-wider">Profile Views</span>
            <span class="text-xl font-black text-blue-400">{{ profile?.views_count ?? 0 }} <span class="text-xs text-slate-400 font-medium">Times</span></span>
          </div>
        </div>
      </div>

      <!-- MAIN GRID CONTAINER LAYOUT (12 Columns Architecture) -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT PANEL: BIODATA INPUT MANAGEMENT (lg:col-span-5) -->
        <div class="lg:col-span-5">
          <form @submit.prevent="submitUpdate" class="bg-white border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xs space-y-5">
            <div>
              <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Modify Biography & Contacts</h3>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">The primary background metrics showcasing on your public digital CV resume.</p>
            </div>

            <!-- Sub-modul Unggah Foto Profil dengan Fitur Comparison & Cancel -->
            <div class="flex flex-col gap-4 p-4 bg-slate-50 border border-slate-200/50 rounded-2xl">
              <div class="flex items-center justify-center sm:justify-start gap-4 flex-wrap">

                <!-- Box Foto Profil Lama -->
                <div class="flex flex-col items-center space-y-1">
                  <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Current Picture</span>
                  <div class="h-20 w-20 rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-inner">
                    <img
                      :src="profile?.profile_picture ? `/storage/profile_picture/${profile.profile_picture}` : '/storage/profile_picture/default.jpg'"
                      class="object-cover w-full h-full"
                      alt="Current Avatar"
                    />
                  </div>
                </div>

                <!-- Arrow Connector Indicator -->
                <div v-if="imagePreview" class="text-slate-300 text-lg font-bold self-center pt-3 select-none">
                  &rarr;
                </div>

                <!-- Box Pratinjau Foto Baru -->
                <div v-if="imagePreview" class="flex flex-col items-center space-y-1 animate-fadeIn">
                  <span class="text-[9px] font-bold text-blue-500 uppercase tracking-wider">New Preview</span>
                  <div class="h-20 w-20 rounded-2xl overflow-hidden border border-blue-300 bg-white shadow-md">
                    <img
                      :src="imagePreview"
                      class="object-cover w-full h-full"
                      alt="New Avatar Preview"
                    />
                  </div>
                </div>
              </div>

              <!-- Kontrol Tombol Aksi Upload, Cancel, dan Remove -->
              <div class="space-y-1.5 text-center sm:text-left pt-1 border-t border-slate-200/40">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Avatar Identity Face</label>
                <input
                  type="file"
                  ref="fileInput"
                  @change="handleFileChange"
                  accept="image/jpeg,image/png,image/jpg"
                  class="hidden"
                />

                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                  <button
                    type="button"
                    @click="triggerFileSelect"
                    class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-[11px] font-bold rounded-xl transition cursor-pointer shadow-2xs"
                  >
                    {{ form.profile_picture ? '🔄 Change Selection' : '📷 Upload New Picture' }}
                  </button>

                  <button
                    v-if="form.profile_picture"
                    type="button"
                    @click="cancelImageSelection"
                    class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-[11px] font-bold rounded-xl transition cursor-pointer"
                  >
                    ❌ Cancel
                  </button>

                  <button
                    v-if="profile?.profile_picture && profile.profile_picture !== 'default.jpg' && profile.profile_picture !== '-'"
                    type="button"
                    @click="form.delete(route('profile.picture.destroy'), { preserveScroll: true })"
                    class="px-3 py-1.5 bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-600 text-[11px] font-bold rounded-xl transition cursor-pointer"
                  >
                    🗑️ Remove Photo
                  </button>
                </div>

                <p class="text-[9px] text-slate-400 font-medium">Accepted extensions: JPG, JPEG, PNG. Maximum limits: **1MB**.</p>
                <span v-if="form.errors.profile_picture" class="text-[10px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.profile_picture }}</span>
              </div>
            </div>

            <div class="space-y-3.5">
              <!-- About My Profile -->
              <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">About My Profile</label>
                <textarea
                  v-model="form.profile"
                  rows="3"
                  placeholder="State a concise summarizing statement highlighting your technical stack..."
                  :class="[form.errors.profile ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition resize-none placeholder:text-slate-400']"
                ></textarea>
                <span v-if="form.errors.profile" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.profile }}</span>
              </div>

              <!-- Core Skills -->
              <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Core Expertise / Skills</label>
                <input
                  v-model="form.skill"
                  type="text"
                  placeholder="Comma separated values: Laravel, Vue.js, Tailwind CSS, Python"
                  :class="[form.errors.skill ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']"
                />
                <span v-if="form.errors.skill" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.skill }}</span>
              </div>

              <!-- Personal Email -->
              <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Personal Communication Email</label>
                <input
                  v-model="form.personal_email"
                  type="email"
                  placeholder="yourname@example.com"
                  :class="[form.errors.personal_email ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']"
                />
                <span v-if="form.errors.personal_email" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.personal_email }}</span>
              </div>

              <!-- WhatsApp & LinkedIn Grid -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">WhatsApp Number</label>
                  <input
                    v-model="form.phone"
                    type="text"
                    placeholder="e.g. +6281277xxxx"
                    :class="[form.errors.phone ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']"
                  />
                  <span v-if="form.errors.phone" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.phone }}</span>
                </div>

                <div class="space-y-1">
                  <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">LinkedIn Profile URL</label>
                  <input
                    v-model="form.linkedin"
                    type="url"
                    placeholder="https://linkedin.com/in/..."
                    :class="[form.errors.linkedin ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']"
                  />
                  <span v-if="form.errors.linkedin" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.linkedin }}</span>
                </div>
              </div>

              <!-- Instagram & X Profile Grid -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Instagram Profile URL</label>
                  <input
                    v-model="form.instagram"
                    type="url"
                    placeholder="https://instagram.com/..."
                    :class="[form.errors.instagram ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']"
                  />
                  <span v-if="form.errors.instagram" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.instagram }}</span>
                </div>

                <div class="space-y-1">
                  <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Twitter / X Profile URL</label>
                  <input
                    v-model="form.twitter"
                    type="url"
                    placeholder="https://x.com/username"
                    :class="[form.errors.twitter ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']"
                  />
                  <span v-if="form.errors.twitter" class="text-xs font-semibold text-rose-500 block mt-0.5">{{ form.errors.twitter }}</span>
                </div>
              </div>

              <!-- Residential Address -->
              <div class="space-y-1.5">
                <label for="address" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Residential Address</label>
                <input
                  id="address"
                  v-model="form.address"
                  type="text"
                  class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:outline-hidden focus:border-blue-600 bg-white transition placeholder:text-slate-400"
                  placeholder="Batam Centre, Riau Islands"
                />
              </div>
            </div>

            <!-- Form Save Button -->
            <div class="flex justify-end pt-2">
              <button
                type="submit"
                :disabled="form.processing"
                class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-black rounded-xl text-[11px] uppercase tracking-wider shadow-xs transition flex items-center justify-center space-x-2 cursor-pointer"
              >
                <span v-if="form.processing" class="h-3 w-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <span>Save Profile Records</span>
              </button>
            </div>
          </form>
        </div>

        <!-- ========================================================= -->
        <!-- MIDDLE PANEL: CHRONOLOGICAL PORTFOLIO FEEDS (lg:col-span-4)-->
        <!-- ========================================================= -->
        <div class="lg:col-span-4 space-y-5">

          <!-- BLOCK 1: EDUCATION HISTORY -->
          <div class="p-5 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
              <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5"><span>🎓</span> Education History</h4>
              <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold">{{ educations?.length ?? 0 }} Logs</span>
            </div>
            <div v-if="educations && educations.length > 0" class="space-y-2.5 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
              <div v-for="edu in educations" :key="edu.id" class="text-xs font-medium bg-slate-50/50 border border-slate-200/40 p-3 rounded-2xl flex justify-between items-center gap-2 animate-fadeIn">
                <div class="space-y-0.5 truncate">
                  <!-- 🌟 SINKRONISASI: Menggunakan edu.level dan edu.major -->
                  <p class="text-slate-800 font-black truncate">
                    {{ edu.level }} <span v-if="edu.major && edu.major !== '-'">— {{ edu.major }}</span>
                  </p>
                  <!-- 🌟 SINKRONISASI: Menggunakan edu.institution -->
                  <p class="text-[10px] text-slate-400 font-bold truncate">Institution: <span class="text-slate-600">{{ edu.institution }}</span></p>
                </div>
                <!-- 🌟 SINKRONISASI: Menggunakan edu.year_end -->
                <span class="text-[10px] text-slate-500 font-bold shrink-0 tabular-nums">{{ edu.year_end }}</span>
              </div>
            </div>
            <p v-else class="text-xs text-slate-400 font-medium py-1 italic">No academic education logs recorded yet.</p>
          </div>

          <!-- BLOCK 2: PROJECT PORTFOLIOS -->
          <div class="p-5 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
              <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5"><span>🚀</span> Project Portfolio</h4>
              <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold">{{ products?.length ?? 0 }} Assets</span>
            </div>
            <div v-if="products && products.length > 0" class="divide-y divide-slate-100 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
              <div v-for="prod in products" :key="prod.id" class="py-2.5 flex items-center justify-between text-xs font-medium first:pt-0 last:pb-0 gap-3">
                <div class="truncate space-y-0.5">
                  <p class="text-slate-800 font-black truncate">{{ prod.title }}</p>
                  <p class="text-[10px] text-slate-400 truncate font-medium">{{ prod.description }}</p>
                </div>
                <a :href="prod.link" target="_blank" class="text-[10px] text-blue-600 font-bold hover:underline shrink-0 bg-blue-50/60 border border-blue-100/30 px-2.5 py-1 rounded-xl shadow-2xs whitespace-nowrap">View ↗</a>
              </div>
            </div>
            <p v-else class="text-xs text-slate-400 font-medium py-1 italic">No interactive project artifact products pinned yet.</p>
          </div>

          <!-- BLOCK 3: PROFESSIONAL EXPERIENCES -->
          <div class="p-5 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
              <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5"><span>💼</span> Professional Experiences</h4>
              <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold">{{ experiences?.length ?? 0 }} Logs</span>
            </div>
            <div v-if="experiences && experiences.length > 0" class="space-y-2.5 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
              <div v-for="exp in experiences" :key="exp.id" class="text-xs font-medium bg-slate-50/50 border border-slate-200/40 p-3 rounded-2xl flex justify-between items-center gap-2">
                <div class="space-y-0.5 truncate">
                  <p class="text-slate-800 font-black truncate">{{ exp.title }}</p>
                  <p class="text-[10px] text-slate-400 font-bold truncate">🏢 {{ exp.company }}</p>
                </div>
                <span class="text-[10px] text-slate-500 font-medium shrink-0 select-none whitespace-nowrap italic">{{ exp.employment_type }}</span>
              </div>
            </div>
            <p class="text-xs text-slate-400 font-medium py-1 italic" v-else>No professional work history logged.</p>
          </div>

          <!-- BLOCK 4: CERTIFICATIONS & TRAINING -->
          <div class="p-5 bg-white border border-slate-200/60 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
              <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5"><span>📜</span> Certifications & Training</h4>
              <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold">{{ certifications?.length ?? 0 }} Badges</span>
            </div>
            <div v-if="certifications && certifications.length > 0" class="divide-y divide-slate-100 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
              <div v-for="cert in certifications" :key="cert.id" class="py-2.5 flex items-center justify-between text-xs font-medium first:pt-0 last:pb-0 gap-3">
                <div class="truncate space-y-0.5">
                  <p class="text-slate-800 font-black truncate">{{ cert.title }}</p>
                  <p class="text-[10px] text-slate-400 truncate font-medium">🏛️ Issuer: {{ cert.institution }}</p>
                </div>
                <a v-if="cert.attachment && cert.attachment !== '-'" :href="cert.attachment" target="_blank" class="text-[10px] bg-slate-100 text-slate-600 px-2.5 py-1 rounded-xl font-bold border border-slate-200 shrink-0 hover:bg-slate-200 shadow-2xs whitespace-nowrap">Review</a>
              </div>
            </div>
            <p v-else class="text-xs text-slate-400 font-medium py-1 italic">No competency credentials registered.</p>
          </div>

        </div>

        <!-- RIGHT PANEL: SYSTEM RECOMMENDATION & ADVISOR (lg:col-span-3) -->
        <div class="lg:col-span-3 space-y-6">
          <div class="bg-white border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xs space-y-4">
            <div>
              <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5"><span>📊</span> Profile Completion</h4>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">Score visibility rank calculation metric stats.</p>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
              <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden border border-slate-200/30">
                <div
                  class="bg-linear-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500"
                  :style="`width: ${completenessPercentage}%`"
                ></div>
              </div>
              <div class="flex justify-between items-center text-xs font-bold">
                <span class="text-slate-500">Completeness</span>
                <span :class="completenessPercentage === 100 ? 'text-green-600' : 'text-blue-600'">{{ completenessPercentage }}%</span>
              </div>
            </div>

            <hr class="border-slate-100" />

            <!-- Missing Task Recommendations -->
            <div class="space-y-3">
              <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Suggested Improvements</h5>

              <div v-if="portfolioSuggestions.length > 0" class="space-y-2.5 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                <div
                  v-for="suggest in portfolioSuggestions"
                  :key="suggest.id"
                  class="p-3 bg-amber-50/60 border border-amber-200/40 rounded-2xl flex items-start gap-2.5"
                >
                  <span class="text-sm shrink-0 select-none">{{ suggest.icon }}</span>
                  <p class="text-[11px] text-amber-800 font-medium leading-normal">{{ suggest.text }}</p>
                </div>
              </div>

              <div v-else class="p-4 bg-green-50 border border-green-200/40 rounded-2xl text-center space-y-1">
                <span class="text-xl">✨</span>
                <h5 class="text-xs font-black text-green-800">Perfect Score Asset!</h5>
                <p class="text-[11px] text-green-600/80 font-medium leading-normal">Your core portfolio segments are entirely complete and fully ready for review channels.</p>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
</style>

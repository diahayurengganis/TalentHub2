<!-- resources/js/Pages/Student/Achievement/Index.vue -->
<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

defineOptions({ layout: StudentLayout });

const props = defineProps({
  student: Object,
  achievements: Array
});

// Initialize form matching your database schema strings perfectly
const form = useForm({
  achievement: '',
  title: '',
  category: '',
  level: '',
  time: '',
  organization: '',
  location: '',
  description: '',
  participant: '',
  attachment: ''
});

const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditId = ref(null);

const openAddModal = () => {
  isEditMode.value = false;
  currentEditId.value = null;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

const openEditModal = (ach) => {
  isEditMode.value = true;
  currentEditId.value = ach.id;
  form.clearErrors();

  form.achievement = ach.achievement;
  form.title = ach.title;
  form.category = ach.category;
  form.level = ach.level;

  // 🌟 PERBAIKAN TOTAL: Memastikan format string dikonversi ke YYYY-MM-DD yang valid untuk HTML5 Input Date
  if (ach.time) {
    const dateObj = new Date(ach.time);
    // Jika formatnya valid, ambil ISO String dan potong bagian tanggalnya saja
    form.time = !isNaN(dateObj.getTime())
      ? dateObj.toISOString().split('T')[0]
      : '';
  } else {
    form.time = '';
  }

  form.organization = ach.organization;
  form.location = ach.location;
  form.description = ach.description;
  form.participant = ach.participant ?? '';
  form.attachment = ach.attachment === '-' ? '' : ach.attachment;

  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditMode.value) {
    form.put(route('achievement.update', currentEditId.value), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  } else {
    form.post(route('achievement.store'), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  }
};

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Delete Achievement Record?',
    text: 'This action will permanently remove this recognition milestone from your public CV portfolio profile.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Delete',
    cancelButtonText: 'Cancel',
    buttonsStyling: false,
    customClass: {
      confirmButton: 'px-4 py-2 bg-rose-600 text-white font-bold rounded-xl text-xs mr-3 cursor-pointer',
      cancelButton: 'px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs cursor-pointer border border-slate-200/40',
      popup: 'rounded-2xl p-6 font-sans tracking-tight'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('achievement.destroy', id));
    }
  });
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('en-US', options);
};
</script>

<template>
  <Head title="Manage Honors & Achievements" />

  <div class="p-4 sm:p-8 bg-slate-50 min-h-screen tracking-tight">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Top Workspace Menu Header -->
      <div class="flex items-center justify-between bg-white border border-slate-200/60 p-5 rounded-3xl shadow-xs">
        <div>
          <h1 class="text-base font-black text-slate-900 uppercase tracking-wide flex items-center gap-1.5"><span>🏆</span> Student Achievements</h1>
          <p class="text-xs text-slate-400 font-medium">Record your competitive winning placements, academic honors, PBL project recognitions, or specialized credentials.</p>
        </div>
        <button
          @click="openAddModal"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer shadow-xs whitespace-nowrap uppercase tracking-wider"
        >
          <span>➕</span> Add Achievement
        </button>
      </div>

      <!-- Chronological Display Layout Grid -->
      <div class="bg-white border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xs">
        <div v-if="achievements.length > 0" class="space-y-4">
          <div
            v-for="ach in achievements"
            :key="ach.id"
            class="bg-slate-50/60 border border-slate-200/40 p-4 rounded-2xl flex flex-col md:flex-row justify-between md:items-center gap-4 hover:shadow-xs transition"
          >
            <div class="space-y-1.5">
              <div class="flex flex-wrap items-center gap-2">
                <span class="text-[10px] font-extrabold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md uppercase tracking-wider border border-blue-100/50">
                  {{ ach.achievement }}
                </span>
                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100/50">
                  📍 {{ ach.level }}
                </span>
                <span class="text-[10px] font-bold text-slate-600 bg-slate-200/60 px-2 py-0.5 rounded-md">
                  {{ ach.category }}
                </span>
              </div>

              <h3 class="text-sm font-black text-slate-900 Lauren leading-tight">{{ ach.title }}</h3>
              <p class="text-xs text-slate-500 font-medium leading-relaxed max-w-3xl">{{ ach.description }}</p>

              <div class="flex flex-wrap text-[11px] text-slate-400 font-bold gap-x-4 gap-y-1 pt-0.5">
                <span>🏢 Organizer / Host: <span class="text-slate-600">{{ ach.organization }}</span></span>
                <span>📅 Date Awarded: <span class="text-slate-600">{{ formatDate(ach.time) }}</span></span>
                <span v-if="ach.participant">👥 Participants Count: <span class="text-slate-600">{{ ach.participant }}</span></span>
              </div>
            </div>

            <!-- Action Trigger Section Block -->
            <div class="pt-3 md:pt-0 border-t md:border-t-0 border-slate-200 flex items-center justify-between md:justify-end gap-3 shrink-0">
              <a v-if="ach.attachment !== '-'" :href="ach.attachment" target="_blank" class="text-xs text-blue-600 font-black hover:underline tracking-wide uppercase">Certificate &rarr;</a>
              <div class="flex items-center space-x-1">
                <button @click="openEditModal(ach)" class="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-[11px] font-bold rounded-xl transition cursor-pointer">Edit</button>
                <button @click="confirmDelete(ach.id)" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[11px] font-bold rounded-xl transition cursor-pointer">Delete</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State Container -->
        <div v-else class="text-center py-12 text-slate-400 text-xs font-medium italic">
          🔒 No competitive achievements or honorary recognition milestones logged within your profile sheets yet.
        </div>
      </div>

    </div>
  </div>

  <!-- ========================================================= -->
  <!-- POP-UP INTERACTION FORM DIALOG MODAL (CREATE & MODIFICATION) -->
  <!-- ========================================================= -->
  <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 animate-fadeIn">
    <div class="bg-white border border-slate-100 w-full max-w-lg rounded-3xl p-6 space-y-5 shadow-xl overflow-y-auto max-h-[90vh] custom-scrollbar">
      <div>
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">
          {{ isEditMode ? '✏️ Synchronize Achievement Record' : '🏆 Append Student Achievement' }}
        </h3>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Populate the competency award parameters matching your original physical certificates.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Award Placement -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Award Distinction / Placement</label>
            <input v-model="form.achievement" type="text" placeholder="e.g. 1st Winner, Best Presentation" :class="[form.errors.achievement ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
            <span v-if="form.errors.achievement" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.achievement }}</span>
          </div>
          <!-- Event Title -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Event / Competition Title</label>
            <input v-model="form.title" type="text" placeholder="e.g. KMIPN VIII, Gemastik Hackathon" :class="[form.errors.title ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
            <span v-if="form.errors.title" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.title }}</span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Category Fields -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Activity Category Track</label>
            <select v-model="form.category" :class="[form.errors.category ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-bold text-slate-600 focus:outline-hidden bg-white cursor-pointer']">
              <option value="" disabled hidden>-- Select Field Category --</option>
              <option value="Contest">Contest / Competition</option>
              <option value="Training">Training / Bootcamps</option>
              <option value="Organization">Organization / Structural Leadership</option>
              <option value="Sains & Teknologi">Science & Technology Research</option>
              <option value="Seni & Olahraga">Arts & Athletic Tournaments</option>
              <option value="Sosial Humaniora">Social Humanities Projects</option>
              <option value="Akademik & Penalaran">Academic Reasoning & Papers</option>
            </select>
            <span v-if="form.errors.category" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.category }}</span>
          </div>
          <!-- Regional Scope Levels -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Regional Scope Level</label>
            <select v-model="form.level" :class="[form.errors.level ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-bold text-slate-600 focus:outline-hidden bg-white cursor-pointer']">
              <option value="" disabled hidden>-- Select Scope Level --</option>
              <option value="Internal Kampus">Campus Internal Track</option>
              <option value="Tingkat Kota / Provinsi">City / Provincial Level</option>
              <option value="Tingkat Nasional">National Level Track</option>
              <option value="Tingkat Internasional">Global / International Level</option>
            </select>
            <span v-if="form.errors.level" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.level }}</span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Calendar Time -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Award / Charter Date</label>
            <input v-model="form.time" type="date" :class="[form.errors.time ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition text-slate-600 cursor-pointer']" />
            <span v-if="form.errors.time" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.time }}</span>
          </div>
          <!-- Total Competitors -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Competitor Count (Optional)</label>
            <input v-model="form.participant" type="number" placeholder="e.g. 150 teams" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:outline-hidden focus:border-blue-600 bg-white transition placeholder:text-slate-400" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Hosting Organization -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Organizing Committee / Host</label>
            <input v-model="form.organization" type="text" placeholder="e.g. Puspresnas, Kemendikbud Ristek" :class="[form.errors.organization ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
            <span v-if="form.errors.organization" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.organization }}</span>
          </div>
          <!-- Event City Venue -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Venue City Location</label>
            <input v-model="form.location" type="text" placeholder="e.g. Bandung, Indonesia" :class="[form.errors.location ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
            <span v-if="form.errors.location" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.location }}</span>
          </div>
        </div>

        <!-- Brief Abstract Summary -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Brief Achievement Summary Description</label>
          <input v-model="form.description" type="text" placeholder="e.g. Innovation award for developing an AI smart sheep lineage breeding app (DombaKu)." :class="[form.errors.description ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.description" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.description }}</span>
        </div>

        <!-- Digital Credential Hyperlink -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Digital Credential Certificate URL</label>
          <input v-model="form.attachment" type="url" placeholder="https://drive.google.com/your-public-shared-certificate-link" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:outline-hidden focus:border-blue-600 transition placeholder:text-slate-400" />
        </div>

        <!-- Tooltip Instructions Context Frame Footer -->
        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-0.5 select-none font-medium text-[10px] text-slate-400 leading-normal">
          <p><span class="text-rose-500 font-bold">*</span> You can utilize institutional domains like <a href="https://polibatam.id" target="_blank" class="text-blue-500 hover:underline">polibatam.id</a> to compress lengthy credential anchor links.</p>
          <p><span class="text-rose-500 font-bold">**</span> Ensure the visibility access permissions of your cloud storage file asset sheets are set to public.</p>
        </div>

        <!-- Interactive Dialog Footer Rows Button Controls -->
        <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
          <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
          <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-xs transition cursor-pointer shadow-xs uppercase tracking-wider">
            <span>{{ form.processing ? 'Processing...' : 'Save Achievement' }}</span>
          </button>
        </div>

      </form>
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

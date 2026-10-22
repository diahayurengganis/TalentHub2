<!-- resources/js/Pages/Student/Experience/Index.vue -->
<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: StudentLayout });

const props = defineProps({
  student: Object,
  experiences: Array
});

const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditId = ref(null);

// Bound string representing today's ISO date used as max barrier restriction
const todayStr = new Date().toISOString().split('T')[0];

const form = useForm({
  title: '',
  company: '',
  location: '',
  employment_type: '',
  start_date: '',
  end_date: '',
  is_current: false
});

const openAddModal = () => {
  isEditMode.value = false;
  currentEditId.value = null;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

const openEditModal = (exp) => {
  isEditMode.value = true;
  currentEditId.value = exp.id;
  form.clearErrors();

  form.title = exp.title;
  form.company = exp.company;
  form.location = exp.location;
  form.employment_type = exp.employment_type;

  form.start_date = exp.start_date ? new Date(exp.start_date).toISOString().split('T')[0] : '';

  // Evaluate structural present tense indicators
  if (!exp.end_date) {
    form.end_date = '';
    form.is_current = true;
  } else {
    form.end_date = new Date(exp.end_date).toISOString().split('T')[0];
    form.is_current = false;
  }

  isModalOpen.value = true;
};

const formatDate = (dateString, isEnd = false) => {
  if (isEnd && !dateString) return 'Present';
  if (!dateString) return '-';
  const options = { year: 'numeric', month: 'short' };
  return new Date(dateString).toLocaleDateString('en-US', options);
};

const submitForm = () => {
  if (isEditMode.value) {
    form.put(route('experiences.update', currentEditId.value), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  } else {
    form.post(route('experiences.store'), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  }
};

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Delete Experience Record?',
    text: 'Discarded employment records cannot be recovered back into your active portfolio timeline.',
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
      router.delete(route('experiences.destroy', id));
    }
  });
};

// 🌟 PERBAIKAN: Logika pengosongan tanggal diproses aman di dalam fungsi setup
const handleStatusChange = () => {
  if (form.is_current) {
    form.end_date = '';
  }
};
</script>

<template>
  <Head title="Professional Experiences" />

  <div class="p-4 sm:p-8 bg-slate-50 min-h-screen tracking-tight">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header Action Panel Bar -->
      <div class="flex items-center justify-between bg-white border border-slate-200/60 p-5 rounded-3xl shadow-xs">
        <div>
          <h1 class="text-base font-black text-slate-900 uppercase tracking-wide flex items-center gap-1.5"><span>💼</span> Professional Experiences</h1>
          <p class="text-xs text-slate-400 font-medium">Manage your historical career records, industrial apprenticeships, corporate internships, or side hustles.</p>
        </div>
        <button
          @click="openAddModal"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer shadow-xs whitespace-nowrap uppercase tracking-wider"
        >
          <span>➕</span> Add Experience
        </button>
      </div>

      <!-- Chronological Activity Layout Grid Feed -->
      <div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-xs">
        <div v-if="experiences.length > 0" class="relative border-l-2 border-slate-100 ml-3 pl-6 space-y-6">
          <div v-for="exp in experiences" :key="exp.id" class="relative group">

            <!-- Activity Bullet Line Node Point Marker -->
            <div class="absolute -left-[31px] top-1.5 h-4 w-4 rounded-full border-2 border-white bg-blue-500 shadow-xs group-hover:bg-indigo-600 transition"></div>

            <div class="bg-slate-50/60 border border-slate-200/40 rounded-2xl p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4 hover:shadow-xs transition">
              <div class="space-y-1">
                <div class="flex items-center space-x-2">
                  <h3 class="text-sm font-black text-slate-900 leading-tight">{{ exp.title }}</h3>
                  <span class="text-[9px] font-extrabold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md uppercase tracking-wider border border-blue-100/50">
                    {{ exp.employment_type }}
                  </span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ exp.company }} <span class="text-slate-300 mx-1">|</span> <span class="text-slate-400">{{ exp.location }}</span></p>
                <p class="text-[11px] text-slate-400 font-bold block pt-0.5">🗓️ Duration: {{ formatDate(exp.start_date) }} — {{ formatDate(exp.end_date, true) }}</p>
              </div>

              <!-- Action Knobs Buttons -->
              <div class="flex items-center space-x-2 shrink-0">
                <button @click="openEditModal(exp)" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-[11px] font-bold rounded-xl transition cursor-pointer">
                  Edit
                </button>
                <button @click="confirmDelete(exp.id)" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[11px] font-bold rounded-xl transition cursor-pointer">
                  Delete
                </button>
              </div>
            </div>

          </div>
        </div>

        <div v-else class="text-center py-12 text-slate-400 text-xs font-medium italic">
          🔒 No historical corporate internship or industrial employment backgrounds logged within your records sheets yet.
        </div>
      </div>

    </div>
  </div>

  <!-- ======================================================= -->
  <!-- POP-UP DIALOG WORKSPACE MODAL (CREATE / EDIT INTERACTION) -->
  <!-- ======================================================= -->
  <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 animate-fadeIn">
    <div class="bg-white border border-slate-100 w-full max-w-md rounded-3xl p-6 space-y-5 shadow-xl">
      <div>
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">
          {{ isEditMode ? '✏️ Synchronize Career Record' : '➕ Append Work Experience' }}
        </h3>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Populate the compliance career metrics parameters details correctly below.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">

        <!-- Job Position Title -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Job Designation / Title</label>
          <input v-model="form.title" type="text" placeholder="e.g. Full-Stack Developer, UI/UX Intern" :class="[form.errors.title ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.title" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.title }}</span>
        </div>

        <!-- Corporate Entity Company -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Company / Firm corporate Name</label>
          <input v-model="form.company" type="text" placeholder="e.g. Infinite Learning, PT. McDermott" :class="[form.errors.company ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.company" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.company }}</span>
        </div>

        <!-- Address Location -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Location Address Venue</label>
          <input v-model="form.location" type="text" placeholder="e.g. Batam, Riau Islands (or Remote)" :class="[form.errors.location ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.location" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.location }}</span>
        </div>

        <!-- Employment Classification Selector Type -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Employment Classification Type</label>
          <select v-model="form.employment_type" :class="[form.errors.employment_type ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-bold text-slate-600 focus:outline-hidden bg-white cursor-pointer']">
            <option value="" disabled hidden>-- Select Appointment Track --</option>
            <option value="Internship">Internship Apprenticeship Track</option>
            <option value="Contract">Contractual Term Track</option>
            <option value="Part-Time">Part-Time Allocation Track</option>
            <option value="Full-Time">Full-Time Core Placement</option>
          </select>
          <span v-if="form.errors.employment_type" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.employment_type }}</span>
        </div>

        <!-- Commencement Calendar Dates Block -->
        <div class="space-y-3">
          <!-- Checkbox Ongoing Indicator Flag -->
          <div class="flex items-center">
            <label class="inline-flex items-center text-xs font-bold text-slate-600 cursor-pointer select-none">
              <!-- 🌟 PERBAIKAN: Memanggil fungsi handleStatusChange -->
              <input
                type="checkbox"
                v-model="form.is_current"
                @change="handleStatusChange"
                class="w-4 h-4 text-blue-600 border-slate-200 rounded-sm focus:ring-blue-500 mr-2 cursor-pointer bg-white"
              />
              I am currently working in this role (Ongoing)
            </label>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <!-- Commencement Date -->
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Commencement Date</label>
              <input
                v-model="form.start_date"
                type="date"
                :max="todayStr"
                :class="[form.errors.start_date ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition text-slate-600 cursor-pointer']"
              />
              <span v-if="form.errors.start_date" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.start_date }}</span>
            </div>

            <!-- Termination Date -->
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Termination Date</label>
              <input
                v-model="form.end_date"
                type="date"
                :max="todayStr"
                :disabled="form.is_current"
                :class="[
                  form.errors.end_date ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600',
                  form.is_current ? 'bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200' : 'bg-white text-slate-600 cursor-pointer',
                  'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition'
                ]"
              />
              <span v-if="form.errors.end_date" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.end_date }}</span>
            </div>
          </div>
        </div>

        <!-- Footer Triggers Submissions Controls -->
        <div class="pt-2 flex justify-end space-x-2">
          <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
          <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-xs transition cursor-pointer shadow-xs uppercase tracking-wider">
            <span>{{ form.processing ? 'Processing...' : 'Save Changes' }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

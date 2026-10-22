<!-- resources/js/Pages/Student/Education/Index.vue -->
<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: StudentLayout });

const props = defineProps({
  student: Object,
  educations: Array
});

// Modal Management States (Create / Edit)
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditId = ref(null);

// Initialize Inertia Form Utilities
const form = useForm({
  level: '',
  institution: '',
  year_start: '',
  year_end: '',
  major: ''
});

// Open modal container to append a NEW record
const openAddModal = () => {
  isEditMode.value = false;
  currentEditId.value = null;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

// Open modal container to MODIFY an existing record
const openEditModal = (edu) => {
  isEditMode.value = true;
  currentEditId.value = edu.id;
  form.clearErrors();

  form.level = edu.level;
  form.institution = edu.institution;
  form.year_start = edu.year_start;
  form.year_end = edu.year_end;
  form.major = edu.major === '-' ? '' : edu.major;

  isModalOpen.value = true;
};

// Form submission pipeline execution dispatch (POST or PUT)
const submitForm = () => {
  if (isEditMode.value) {
    form.put(route('education.update', currentEditId.value), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  } else {
    form.post(route('education.store'), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  }
};

// Prompt asset removal verification dialog via SweetAlert2
const confirmDelete = (id) => {
  Swal.fire({
    title: 'Delete Education History?',
    text: 'Discarded record values cannot be recovered safely back into your portfolio database.',
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
      router.delete(route('education.destroy', id));
    }
  });
};
</script>

<template>
  <Head title="Manage Education History" />

  <div class="p-4 sm:p-8 bg-slate-50 min-h-screen tracking-tight">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header Action Bar Menu -->
      <div class="flex items-center justify-between bg-white border border-slate-200/60 p-5 rounded-3xl shadow-xs">
        <div>
          <h1 class="text-base font-black text-slate-900 uppercase tracking-wide flex items-center gap-1.5"><span>🎓</span> Education Background</h1>
          <p class="text-xs text-slate-400 font-medium">Manage your formal academic history background records from high school tracks to higher education.</p>
        </div>
        <button
          @click="openAddModal"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer shadow-xs whitespace-nowrap uppercase tracking-wider"
        >
          <span>➕</span> Add History
        </button>
      </div>

      <!-- Chronological Timeline Main Feed Box Layout -->
      <div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-xs">
        <div v-if="educations.length > 0" class="relative border-l-2 border-slate-100 ml-3 pl-6 space-y-6">
          <div v-for="edu in educations" :key="edu.id" class="relative group">

            <!-- Timeline Bullet Activity Point Marker -->
            <div class="absolute -left-[31px] top-1.5 h-4 w-4 rounded-full border-2 border-white bg-blue-500 shadow-xs group-hover:bg-indigo-600 transition"></div>

            <div class="bg-slate-50/60 border border-slate-200/40 rounded-2xl p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4 hover:shadow-xs transition">
              <div class="space-y-1">
                <span class="text-[10px] font-extrabold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-md uppercase tracking-wider border border-blue-100/50">
                  {{ edu.level }}
                </span>
                <h3 class="text-sm font-black text-slate-900 mt-1.5">{{ edu.institution }}</h3>
                <p v-if="edu.major !== '-'" class="text-xs text-slate-500 font-medium">Major / Specialization: <span class="font-bold text-slate-700">{{ edu.major }}</span></p>
                <p class="text-[11px] text-slate-400 font-bold block pt-0.5">🗓️ Period Term: {{ edu.year_start }} — {{ edu.year_end }}</p>
              </div>

              <!-- Management Action Knobs -->
              <div class="flex items-center space-x-2 shrink-0">
                <button @click="openEditModal(edu)" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-[11px] font-bold rounded-xl transition cursor-pointer">
                  ✏️ Edit
                </button>
                <button @click="confirmDelete(edu.id)" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[11px] font-bold rounded-xl transition cursor-pointer">
                  🗑️ Delete
                </button>
              </div>
            </div>

          </div>
        </div>

        <!-- Fallback Empty State Area Box -->
        <div v-else class="text-center py-12 text-slate-400 text-xs font-medium italic">
          🔒 No formal educational history milestones recorded within your TalentHub portfolio profile sheet database yet.
        </div>
      </div>

    </div>
  </div>

  <!-- ========================================== -->
  <!-- POP-UP INPUT INTERACTION DIALOG DIALOG MODAL-->
  <!-- ========================================== -->
  <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 animate-fadeIn">
    <div class="bg-white border border-slate-100 w-full max-w-md rounded-3xl p-6 space-y-5 shadow-xl">
      <div>
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">
          {{ isEditMode ? '✏️ Synchronize Education Record' : '➕ Append Education Record' }}
        </h3>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Populate the graduate compliance parameters accurately below.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">

        <!-- Degree Tier Level Selection -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Degree Qualification Level</label>
          <select
            v-model="form.level"
            :class="[form.errors.level ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-bold text-slate-600 focus:outline-hidden transition bg-white cursor-pointer']"
          >
            <option value="" disabled hidden>-- Select Degree Level --</option>
            <option value="SMA / SMK / MA">High School Degree (SMA / SMK / MA)</option>
            <option value="Diploma III (D3)">Associate Degree (Diploma III - D3)</option>
            <option value="Diploma IV / Sarjana (D4/S1)">Bachelor Graduate Degree (Diploma IV / Sarjana - D4/S1)</option>
            <option value="Magister (S2)">Master Degree Graduate (Magister - S2)</option>
          </select>
          <span v-if="form.errors.level" class="text-[11px] font-semibold text-rose-500 block">⚠️ {{ form.errors.level }}</span>
        </div>

        <!-- Institution Name Box -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">School / University Institution Name</label>
          <input v-model="form.institution" type="text" placeholder="e.g. Politeknik Negeri Batam" :class="[form.errors.institution ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.institution" class="text-[11px] font-semibold text-rose-500 block">⚠️ {{ form.errors.institution }}</span>
        </div>

        <!-- Major Specialization Field Input -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Major Program / Core Competency Field</label>
          <input v-model="form.major" type="text" placeholder="e.g. Informatics Engineering (Leave blank if none)" :class="[form.errors.major ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.major" class="text-[11px] font-semibold text-rose-500 block">⚠️ {{ form.errors.major }}</span>
        </div>

        <!-- Dual Column Period Input Grid -->
        <div class="grid grid-cols-2 gap-3">
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Commencement Year</label>
            <input v-model="form.year_start" type="number" placeholder="e.g. 2022" :class="[form.errors.year_start ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
            <span v-if="form.errors.year_start" class="text-[11px] font-semibold text-rose-500 block">⚠️ {{ form.errors.year_start }}</span>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Graduation Year</label>
            <input v-model="form.year_end" type="number" placeholder="e.g. 2026" :class="[form.errors.year_end ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
            <span v-if="form.errors.year_end" class="text-[11px] font-semibold text-rose-500 block">⚠️ {{ form.errors.year_end }}</span>
          </div>
        </div>

        <!-- Trigger Button Dialog Row footer -->
        <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
          <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition cursor-pointer">
            Cancel
          </button>
          <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-xs transition cursor-pointer shadow-xs uppercase tracking-wider">
            <span>{{ form.processing ? 'Processing...' : 'Save Changes' }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

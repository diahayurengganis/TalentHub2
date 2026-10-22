<!-- resources/js/Pages/Student/Certification/Index.vue -->
<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: StudentLayout });

const props = defineProps({
  student: Object,
  certifications: Array
});

const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditId = ref(null);

// Bound string representing today's ISO date used as max barrier restriction
const todayStr = new Date().toISOString().split('T')[0];

const form = useForm({
  title: '',
  institution: '',
  description: '',
  start_date: '',
  end_date: '',
  is_lifetime: false
});

const openAddModal = () => {
  isEditMode.value = false;
  currentEditId.value = null;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

const openEditModal = (cert) => {
  isEditMode.value = true;
  currentEditId.value = cert.id;
  form.clearErrors();

  form.title = cert.title;
  form.institution = cert.institution;
  form.description = cert.description;
  form.attachment = cert.attachment === '-' ? '' : cert.attachment;

  form.start_date = cert.start_date ? new Date(cert.start_date).toISOString().split('T')[0] : '';

  if (!cert.end_date) {
    form.end_date = '';
    form.is_lifetime = true;
  } else {
    form.end_date = new Date(cert.end_date).toISOString().split('T')[0];
    form.is_lifetime = false;
  }

  isModalOpen.value = true;
};

const handleLifetimeChange = () => {
  if (form.is_lifetime) {
    form.end_date = '';
  }
};

const submitForm = () => {
  if (isEditMode.value) {
    form.put(route('certifications.update', currentEditId.value), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  } else {
    form.post(route('certifications.store'), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  }
};

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Delete Certification?',
    text: 'This action will permanently strip this credential tracking validation from your public portfolio layout.',
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
      router.delete(route('certifications.destroy', id));
    }
  });
};

const formatDateRange = (start, end) => {
  if (!start) return '-';
  const options = { year: 'numeric', month: 'short' };
  const startFormatted = new Date(start).toLocaleDateString('en-US', options);
  const endFormatted = end ? new Date(end).toLocaleDateString('en-US', options) : 'No Expiry (Lifetime)';
  return `${startFormatted} — ${endFormatted}`;
};
</script>

<template>
  <Head title="Manage Certifications" />

  <div class="p-4 sm:p-8 bg-slate-50 min-h-screen tracking-tight">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header Workspace Action Card Row -->
      <div class="flex items-center justify-between bg-white border border-slate-200/60 p-5 rounded-3xl shadow-xs">
        <div>
          <h1 class="text-base font-black text-slate-900 uppercase tracking-wide flex items-center gap-1.5"><span>📜</span> Professional Certifications</h1>
          <p class="text-xs text-slate-400 font-medium">Exhibit your industrial competence licenses, course completions, vendor credentials, or bootcamp skill badges.</p>
        </div>
        <button
          @click="openAddModal"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer shadow-xs whitespace-nowrap uppercase tracking-wider"
        >
          <span>➕</span> Add Certificate
        </button>
      </div>

      <!-- Bento-style Grid Feed Container -->
      <div class="bg-white border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xs">
        <div v-if="certifications.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="cert in certifications"
            :key="cert.id"
            class="bg-slate-50/60 border border-slate-200/40 p-4 rounded-2xl flex justify-between items-start hover:shadow-xs transition"
          >
            <div class="space-y-1.5 truncate pr-2">
              <div class="flex items-center space-x-2">
                <span class="text-[9px] font-extrabold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100/50 uppercase tracking-wider">
                  Active
                </span>
                <h4 class="font-black text-slate-800 text-sm truncate tracking-tight">{{ cert.title }}</h4>
              </div>
              <p class="text-xs text-slate-500 font-bold truncate">Authority: <span class="text-slate-700">{{ cert.institution }}</span></p>
              <p class="text-[11px] text-slate-400 font-bold">🗓️ Validity: {{ formatDateRange(cert.start_date, cert.end_date) }}</p>
              <p class="text-[11px] text-slate-400 font-medium line-clamp-2 leading-relaxed whitespace-normal pt-1">{{ cert.description }}</p>
            </div>

            <!-- Configuration Adjustment Buttons -->
            <div class="flex items-center space-x-1 shrink-0 ml-2">
              <a v-if="cert.attachment !== '-'" :href="cert.attachment" target="_blank" class="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-[10px] font-bold rounded-xl transition">Link ↗</a>
              <button @click="openEditModal(cert)" class="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 text-[10px] font-bold rounded-xl transition cursor-pointer">✏️</button>
              <button @click="confirmDelete(cert.id)" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-bold rounded-xl transition cursor-pointer">🗑️</button>
            </div>
          </div>
        </div>

        <div class="text-center py-12 text-slate-400 text-xs font-medium italic" v-else>
          🔒 No external certified vendor credentials or course achievements registered inside your portfolio folder yet.
        </div>
      </div>

    </div>
  </div>

  <!-- ======================================================== -->
  <!-- DIALOG POP-UP INTERACTION BOX MODAL (CREATE/MODIFICATION) -->
  <!-- ======================================================== -->
  <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 animate-fadeIn">
    <div class="bg-white border border-slate-100 w-full max-w-md rounded-3xl p-6 space-y-5 shadow-xl">
      <div>
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">
          {{ isEditMode ? '✏️ Modify Certification Record' : '➕ Append Professional Certificate' }}
        </h3>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Populate the license authorization metrics precisely below.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">

        <!-- Certificate Title Input Box -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Certificate Designation / Title</label>
          <input v-model="form.title" type="text" placeholder="e.g. AWS Certified Cloud Practitioner, TOEFL ITP" :class="[form.errors.title ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.title" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.title }}</span>
        </div>

        <!-- Granting Institution Body -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Issuing Authority / Institution</label>
          <input v-model="form.institution" type="text" placeholder="e.g. Amazon Web Services, Cisco, ETS" :class="[form.errors.institution ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.institution" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.institution }}</span>
        </div>

        <!-- Date Allocation Block -->
        <div class="space-y-3">
          <div class="flex items-center">
            <label class="inline-flex items-center text-xs font-bold text-slate-600 cursor-pointer select-none">
              <input
                type="checkbox"
                v-model="form.is_lifetime"
                @change="handleLifetimeChange"
                class="w-4 h-4 text-blue-600 border-slate-200 rounded-sm focus:ring-blue-500 mr-2 cursor-pointer bg-white"
              />
              This credential does not expire (Lifetime)
            </label>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <!-- Issue Date -->
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Issuance Date</label>
              <input
                v-model="form.start_date"
                type="date"
                :max="todayStr"
                :class="[form.errors.start_date ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition text-slate-600 cursor-pointer']"
              />
              <span v-if="form.errors.start_date" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.start_date }}</span>
            </div>

            <!-- Expiry Date -->
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Expiration Date</label>
              <input
                v-model="form.end_date"
                type="date"
                :disabled="form.is_lifetime"
                :class="[
                  form.errors.end_date ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600',
                  form.is_lifetime ? 'bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200' : 'bg-white text-slate-600 cursor-pointer',
                  'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition'
                ]"
              />
              <span v-if="form.errors.end_date" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.end_date }}</span>
            </div>
          </div>
        </div>

        <!-- Description Paragraph Field Area -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Brief Core Syllabus Description</label>
          <textarea v-model="form.description" rows="3" placeholder="Summarize core fields tested..." :class="[form.errors.description ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition resize-none placeholder:text-slate-400']"></textarea>
          <span v-if="form.errors.description" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.description }}</span>
        </div>

        <!-- Anchor Document Hyperlinks File Box Input -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Digital Validation URL Link</label>
          <input v-model="form.attachment" type="url" placeholder="https://credly.com/your-badge-shares-link" :class="[form.errors.attachment ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.attachment" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.attachment }}</span>
        </div>

        <!-- Tooltip Context Instructions Banner Footer -->
        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-0.5 select-none font-medium text-[10px] text-slate-400 leading-normal">
          <p><span class="text-rose-500 font-bold">*</span> You can utilize institutional domains like <a href="https://polibatam.id" target="_blank" class="text-blue-500 hover:underline">polibatam.id</a> to compress lengthy credential anchor links.</p>
          <p><span class="text-rose-500 font-bold">**</span> Ensure the visibility access permissions of your cloud storage file asset sheets are set to public.</p>
        </div>

        <!-- Submission Control Options Rows Row Footer -->
        <div class="pt-2 flex justify-end space-x-2">
          <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
          <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-xs transition cursor-pointer shadow-xs uppercase tracking-wider">
            <span>{{ form.processing ? 'Processing...' : 'Save Credential' }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

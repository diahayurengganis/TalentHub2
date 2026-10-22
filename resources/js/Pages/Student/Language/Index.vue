<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: StudentLayout });

const props = defineProps({
  student: Object,
  languages: Array
});

const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditId = ref(null);

const form = useForm({
  language: '',
  is_active: true,
  score: '',
  certificate: '',
  attachment: ''
});

const openAddModal = () => {
  isEditMode.value = false;
  currentEditId.value = null;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

const openEditModal = (lang) => {
  isEditMode.value = true;
  currentEditId.value = lang.id;
  form.clearErrors();

  // Ambil data dari item pertama array attachments hasil eager load controller
  const attachData = lang.attachments && lang.attachments.length > 0 ? lang.attachments[0] : {};

  form.language = lang.language;
  form.is_active = lang.is_active;
  form.score = attachData.score ?? '';
  form.certificate = attachData.certificate ?? '';
  form.attachment = attachData.attachment ?? '';

  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditMode.value) {
    form.put(route('languages.update', currentEditId.value), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  } else {
    form.post(route('languages.store'), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  }
};

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Delete Language Record?',
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
      router.delete(route('languages.destroy', id));
    }
  });
};
</script>

<template>
  <Head title="Language Proficiencies" />

  <div class="p-4 sm:p-8 bg-slate-50 min-h-screen tracking-tight">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header Menu Action Bar -->
      <div class="flex items-center justify-between bg-white border border-slate-200/60 p-5 rounded-3xl shadow-xs">
        <div>
          <h1 class="text-base font-black text-slate-900 uppercase tracking-wide flex items-center gap-1.5"><span>📖</span> Language Proficiencies</h1>
          <p class="text-xs text-slate-400 font-medium">Map your multi-language speaking skills, linguistic fluencies, or international cert score markers.</p>
        </div>
        <button
          @click="openAddModal"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer shadow-xs whitespace-nowrap uppercase tracking-wider"
        >
          <span>➕</span> Add Language
        </button>
      </div>

      <!-- Main Activity List Map Panel -->
      <div class="bg-white border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xs">
        <div v-if="languages.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="lang in languages"
            :key="lang.id"
            class="bg-slate-50/60 border border-slate-200/40 p-4 rounded-2xl flex justify-between items-center group transition"
          >
            <div class="space-y-1.5">
              <div class="flex items-center space-x-2">
                <h4 class="font-black text-slate-900 text-sm tracking-tight">{{ lang.language }}</h4>
                <span
                  class="text-[9px] font-extrabold px-2 py-0.5 rounded-md uppercase tracking-wider border"
                  :class="lang.is_active
                    ? 'text-blue-700 bg-blue-50 border-blue-100/50'
                    : 'text-slate-500 bg-slate-100 border-slate-200/60'"
                >
                  {{ lang.is_active ? 'Active' : 'Passive' }}
                </span>
              </div>
              <p v-if="lang.attachments && lang.attachments.length > 0" class="text-xs text-slate-400 font-bold">
                Assessed Score Check: <span class="text-slate-700 font-black">{{ lang.attachments[0].score }}</span>
              </p>
            </div>

            <!-- Operational Control Knobs -->
            <div class="flex items-center space-x-1.5 shrink-0 ml-4">
              <template v-if="lang.attachments && lang.attachments.length > 0">
                <a :href="lang.attachments[0].certificate" target="_blank" class="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-[10px] font-bold rounded-xl transition">📜 Cert</a>
                <a :href="lang.attachments[0].attachment" target="_blank" class="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-[10px] font-bold rounded-xl transition">🔗 File</a>
              </template>
              <button @click="openEditModal(lang)" class="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 text-[10px] font-bold rounded-xl transition cursor-pointer">✏️</button>
              <button @click="confirmDelete(lang.id)" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-bold rounded-xl transition cursor-pointer">🗑️</button>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-12 text-slate-400 text-xs font-medium italic">
          🔒 No linguistic or language capability matrices logged into your portfolio dashboard yet.
        </div>
      </div>

    </div>
  </div>

  <!-- ========================================== -->
  <!-- MODAL DIALOG INTERACTION POP-UP BOX        -->
  <!-- ========================================== -->
  <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 animate-fadeIn">
    <div class="bg-white border border-slate-100 w-full max-w-md rounded-3xl p-6 space-y-5 shadow-xl">
      <div>
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">
          {{ isEditMode ? '✏️ Synchronize Language Matrix' : '➕ Append Language Metric' }}
        </h3>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Populate speaking fluency scales and validation records securely below.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">

        <!-- Language Name Field -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Language Name</label>
          <input v-model="form.language" type="text" placeholder="e.g. English, Japanese, German" :class="[form.errors.language ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.language" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.language }}</span>
        </div>

        <!-- Radio Buttons Status Active/Passive -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Fluency Status Type</label>
          <div class="flex items-center space-x-5 pt-1">
            <label class="inline-flex items-center text-xs font-bold text-slate-600 cursor-pointer select-none">
              <input type="radio" :value="true" v-model="form.is_active" class="w-4 h-4 text-blue-600 border-slate-200 focus:ring-blue-500 mr-1.5 cursor-pointer bg-white" />
              Active Speaker
            </label>
            <label class="inline-flex items-center text-xs font-bold text-slate-600 cursor-pointer select-none">
              <input type="radio" :value="false" v-model="form.is_active" class="w-4 h-4 text-blue-600 border-slate-200 focus:ring-blue-500 mr-1.5 cursor-pointer bg-white" />
              Passive Speaker
            </label>
          </div>
        </div>

        <!-- Score Input -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Examination Score Level / Tier</label>
          <input v-model="form.score" type="text" placeholder="e.g. 500, N3, 6.5" :class="[form.errors.score ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.score" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.score }}</span>
        </div>

        <!-- Certificate Links Fields -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">URL Link to Certificate</label>
          <input v-model="form.certificate" type="url" placeholder="https://drive.google.com/..." :class="[form.errors.certificate ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.certificate" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.certificate }}</span>
        </div>

        <!-- Attachment Link Field -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">URL Link to Attachment Documentation</label>
          <input v-model="form.attachment" type="url" placeholder="https://drive.google.com/..." :class="[form.errors.attachment ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.attachment" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.attachment }}</span>
        </div>

        <!-- Tooltip Context Instructions Banner Footer -->
        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-0.5 select-none font-medium text-[10px] text-slate-400 leading-normal">
          <p><span class="text-rose-500 font-bold">*</span> You can utilize institutional domains like <a href="https://polibatam.id" target="_blank" class="text-blue-500 hover:underline">polibatam.id</a> to compress lengthy credential anchor links.</p>
          <p><span class="text-rose-500 font-bold">**</span> Ensure the visibility access permissions of your cloud storage file asset sheets are set to public.</p>
        </div>

        <!-- Trigger Buttons Options Bar -->
        <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
          <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
          <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-xs transition cursor-pointer shadow-xs uppercase tracking-wider">
            <span>{{ form.processing ? 'Processing...' : 'Save Matrix' }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

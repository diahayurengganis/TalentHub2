<!-- resources/js/Pages/Student/Reference/Index.vue -->
<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: StudentLayout });

const props = defineProps({
  student: Object,
  references: Array
});

const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditId = ref(null);

const form = useForm({
  name: '',
  title: '',
  institution: '',
  email: '',
  telp: ''
});

const openAddModal = () => {
  isEditMode.value = false;
  currentEditId.value = null;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

const openEditModal = (refItem) => {
  isEditMode.value = true;
  currentEditId.value = refItem.id;
  form.clearErrors();

  form.name = refItem.name;
  form.title = refItem.title;
  form.institution = refItem.institution;
  form.email = refItem.email;
  form.telp = refItem.telp;

  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditMode.value) {
    form.put(route('references.update', currentEditId.value), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  } else {
    form.post(route('references.store'), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  }
};

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Delete Reference Contact?',
    text: 'This action will drop this corporate/academic validator endorsement from your resume timeline.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Remove',
    cancelButtonText: 'Cancel',
    buttonsStyling: false,
    customClass: {
      confirmButton: 'px-4 py-2 bg-rose-600 text-white font-bold rounded-xl text-xs mr-3 cursor-pointer',
      cancelButton: 'px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs cursor-pointer border border-slate-200/40',
      popup: 'rounded-2xl p-6 font-sans tracking-tight'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('references.destroy', id));
    }
  });
};
</script>

<template>
  <Head title="Professional References - TalentHub" />

  <div class="p-4 sm:p-8 bg-slate-50 min-h-screen tracking-tight">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header Interaction Action Control Menu Bar -->
      <div class="flex items-center justify-between bg-white border border-slate-200/60 p-5 rounded-3xl shadow-xs">
        <div>
          <h1 class="text-base font-black text-slate-900 uppercase tracking-wide flex items-center gap-1.5"><span>📊</span> Professional References</h1>
          <p class="text-xs text-slate-400 font-medium">Log your academic mentors, laboratory coordinators, or corporate internship supervisors to validate your traits.</p>
        </div>
        <button
          @click="openAddModal"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer shadow-xs whitespace-nowrap uppercase tracking-wider"
        >
          <span>➕</span> Add Reference
        </button>
      </div>

      <!-- Main Bento Grid Loop Showcase Display -->
      <div class="bg-white border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xs">
        <div v-if="references.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="refItem in references"
            :key="refItem.id"
            class="bg-slate-50/60 border border-slate-200/40 p-5 rounded-2xl flex flex-col justify-between hover:shadow-xs group transition"
          >
            <div class="space-y-3">
              <div class="flex items-start justify-between gap-2">
                <div>
                  <h4 class="font-black text-slate-900 text-sm tracking-tight leading-snug">{{ refItem.name }}</h4>
                  <p class="text-xs text-blue-600 font-bold mt-0.5">{{ refItem.title }}</p>
                </div>
                <span class="text-[9px] font-extrabold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md uppercase tracking-wider border border-slate-200 shrink-0 select-none">Referee</span>
              </div>

              <!-- Contact Parameters Info List -->
              <div class="space-y-1.5 text-xs text-slate-500 font-medium pt-2 border-t border-slate-200/40">
                <p class="flex items-center gap-2"><span>🏢</span> {{ refItem.institution }}</p>
                <p class="flex items-center gap-2"><span>📧</span> <span class="text-slate-600 font-semibold">{{ refItem.email }}</span></p>
                <p class="flex items-center gap-2"><span>📞</span> <span class="text-slate-600 font-semibold">{{ refItem.telp }}</span></p>
              </div>
            </div>

            <!-- Configuration Operational Triggers -->
            <div class="pt-4 border-t border-slate-200/40 mt-4 flex items-center justify-end space-x-1.5">
              <button @click="openEditModal(refItem)" class="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 text-[10px] font-bold rounded-xl transition cursor-pointer">✏️ Edit</button>
              <button @click="confirmDelete(refItem.id)" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-bold rounded-xl transition cursor-pointer">🗑️ Delete</button>
            </div>
          </div>
        </div>

        <!-- Fallback Empty Grid Flag Sheet -->
        <div v-else class="text-center py-12 text-slate-400 text-xs font-medium italic">
          🔒 No professional endorsers or reference contacts pinned on your portfolio folders yet.
        </div>
      </div>

    </div>
  </div>

  <!-- ======================================================== -->
  <!-- POP-UP WORKSPACE MODEL WINDOW DIALOG (CREATE / MODIFY)   -->
  <!-- ======================================================== -->
  <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 animate-fadeIn">
    <div class="bg-white border border-slate-100 w-full max-w-md rounded-3xl p-6 space-y-5 shadow-xl">
      <div>
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">
          {{ isEditMode ? '✏️ Synchronize Reference Contact' : '➕ Append Professional Endorser' }}
        </h3>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Fill down the verified contact attributes parameters correctly below.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">

        <!-- Referee Full Name -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Referee Full Name</label>
          <input v-model="form.name" type="text" placeholder="e.g. Pak Supardianto, S.S., M.Pd." :class="[form.errors.name ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.name" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.name }}</span>
        </div>

        <!-- Professional Title Position -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Job Title Position Designation</label>
          <input v-model="form.title" type="text" placeholder="e.g. Lecturer Mentor, Head of Laboratory" :class="[form.errors.title ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.title" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.title }}</span>
        </div>

        <!-- Corporate Entity Institution -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Institution / Organization Name</label>
          <input v-model="form.institution" type="text" placeholder="e.g. Politeknik Negeri Batam" :class="[form.errors.institution ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.institution" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.institution }}</span>
        </div>

        <!-- Communication Grid Channel Contacts -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <!-- Email Contact -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Institutional Email</label>
            <input v-model="form.email" type="email" placeholder="referee@polibatam.ac.id" :class="[form.errors.email ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
            <span v-if="form.errors.email" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.email }}</span>
          </div>

          <!-- Telephone / WhatsApp Number -->
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Telephone / WA Number</label>
            <input v-model="form.telp" type="text" placeholder="e.g. +62812xxxxxx" :class="[form.errors.telp ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
            <span v-if="form.errors.telp" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.telp }}</span>
          </div>
        </div>

        <!-- Security Guideline Note Context -->
        <div class="bg-blue-50/60 p-3 rounded-2xl border border-blue-100/50 select-none font-medium text-[10px] text-blue-800 leading-normal">
          <p><span>ℹ️</span> Please ensure you have gained full explicit privacy consent from the target reference person prior to exposing their active contact rows into the search directory visibility pools.</p>
        </div>

        <!-- Trigger buttons options footers -->
        <div class="pt-2 flex justify-end space-x-2">
          <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
          <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-xs transition cursor-pointer shadow-xs uppercase tracking-wider">
            <span>{{ form.processing ? 'Processing...' : 'Save Endorsement' }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

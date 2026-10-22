<!-- resources/js/Pages/Student/Product/Index.vue -->
<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: StudentLayout });

const props = defineProps({
  student: Object,
  products: Array
});

const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditId = ref(null);

const form = useForm({
  title: '',
  description: '',
  link: ''
});

const openAddModal = () => {
  isEditMode.value = false;
  currentEditId.value = null;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

const openEditModal = (prod) => {
  isEditMode.value = true;
  currentEditId.value = prod.id;
  form.clearErrors();

  form.title = prod.title;
  form.description = prod.description;
  form.link = prod.link;

  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditMode.value) {
    form.put(route('products.update', currentEditId.value), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  } else {
    form.post(route('products.store'), {
      onSuccess: () => { isModalOpen.value = false; form.reset(); }
    });
  }
};

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Delete Project Product?',
    text: 'This action will drop this interactive application artifact from your digital showroom display catalog.',
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
      router.delete(route('products.destroy', id));
    }
  });
};
</script>

<template>
  <Head title="Project Portfolios" />

  <div class="p-4 sm:p-8 bg-slate-50 min-h-screen tracking-tight">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header Interaction Action Control Menu Bar -->
      <div class="flex items-center justify-between bg-white border border-slate-200/60 p-5 rounded-3xl shadow-xs">
        <div>
          <h1 class="text-base font-black text-slate-900 uppercase tracking-wide flex items-center gap-1.5"><span>💿</span> Project Portfolios</h1>
          <p class="text-xs text-slate-400 font-medium">Claim, catalogue, and exhibit software products, websites, embedded systems, or anything built within academic period.</p>
        </div>
        <button
          @click="openAddModal"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer shadow-xs whitespace-nowrap uppercase tracking-wider"
        >
          <span>➕</span> Add Product
        </button>
      </div>

      <!-- Main Bento Grid Loop Showcase Display -->
      <div class="bg-white border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xs">
        <div v-if="products.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="prod in products"
            :key="prod.id"
            class="bg-slate-50/60 border border-slate-200/40 p-5 rounded-2xl flex flex-col justify-between hover:shadow-xs group transition"
          >
            <div class="space-y-2">
              <div class="flex items-start justify-between gap-2">
                <h4 class="font-black text-slate-900 text-sm tracking-tight leading-snug group-hover:text-blue-600 transition">{{ prod.title }}</h4>
                <span class="text-[9px] font-extrabold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md uppercase tracking-wider border border-blue-100/50 shrink-0 select-none">Artifact</span>
              </div>
              <p class="text-xs text-slate-500 font-medium leading-relaxed whitespace-normal">{{ prod.description }}</p>
            </div>

            <!-- Configuration Adjustment Rows Button footer links -->
            <div class="pt-5 border-t border-slate-200/40 mt-4 flex items-center justify-between gap-3">
              <a :href="prod.link" target="_blank" class="text-[11px] text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                Open Project Pipeline <span>&rarr;</span>
              </a>
              <div class="flex items-center space-x-1">
                <button @click="openEditModal(prod)" class="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 text-[10px] font-bold rounded-xl transition cursor-pointer">✏️</button>
                <button @click="confirmDelete(prod.id)" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-bold rounded-xl transition cursor-pointer">🗑️</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Fallback Empty Grid Flag Sign Sheet -->
        <div v-else class="text-center py-12 text-slate-400 text-xs font-medium italic">
          🔒 No interactive product artifacts or software systems pinned on your cv directories yet.
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
          {{ isEditMode ? '✏️ Synchronize Product Asset' : '➕ Claim Project Artifact' }}
        </h3>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Fill down the product architectural deployment specs information links below.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">

        <!-- Product Title Name -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Product / Application Name</label>
          <input v-model="form.title" type="text" placeholder="e.g. LabBuddy Bot Chatbot, DombaKu Lineage Tracker" :class="[form.errors.title ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.title" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.title }}</span>
        </div>

        <!-- Description Project Input Textarea -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Project Overview / Description</label>
          <textarea v-model="form.description" rows="4" placeholder="Briefly outline what the product does, your tech stack role, problems solved, and the targeted user base..." :class="[form.errors.description ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition resize-none placeholder:text-slate-400']"></textarea>
          <span v-if="form.errors.description" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.description }}</span>
        </div>

        <!-- Target Link URL Anchor -->
        <div class="space-y-1">
          <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Production Live URL / Source Code Link</label>
          <input v-model="form.link" type="url" placeholder="https://github.com/... or https://your-app.com" :class="[form.errors.link ? 'border-rose-500 focus:border-rose-600' : 'border-slate-200 focus:border-blue-600', 'w-full px-3 py-2.5 border rounded-xl text-xs font-medium focus:outline-hidden transition placeholder:text-slate-400']" />
          <span v-if="form.errors.link" class="text-[11px] font-semibold text-rose-500 block mt-0.5">⚠️ {{ form.errors.link }}</span>
        </div>

        <!-- Context Guidelines Banner Tooltip -->
        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-0.5 select-none font-medium text-[10px] text-slate-400 leading-normal">
          <p><span class="text-rose-500 font-bold">*</span> You can utilize institutional domains like <a href="https://polibatam.id" target="_blank" class="text-blue-500 hover:underline">polibatam.id</a> to compress lengthy credential anchor links.</p>
          <p><span class="text-rose-500 font-bold">**</span> Make sure your shared GitHub repositories code branches visibility permissions are set to public channels.</p>
        </div>

        <!-- Trigger buttons options footers -->
        <div class="pt-2 flex justify-end space-x-2">
          <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
          <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-xs transition cursor-pointer shadow-xs uppercase tracking-wider">
            <span>{{ form.processing ? 'Processing...' : 'Save Product' }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

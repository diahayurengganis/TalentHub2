<!-- resources/js/Pages/Landing/Welcome.vue -->
<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';

defineOptions({ layout: LandingLayout });

const props = defineProps({
  stats: Object,
  students: Array
});

// Penampung data lowongan statis
const internOffers = ref([]);

// State untuk interaktivitas FAQ Accordion
const activeFaq = ref(null);
const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index;
};

// Form Handler Kontak
const contactForm = useForm({
  name: '',
  email: '',
  subject: '',
  message: ''
});

const submitContact = () => {
  contactForm.post(route('contact.send'), {
    preserveScroll: true,
    onSuccess: () => contactForm.reset()
  });
};
</script>

<template>
  <Head title="Integrated Career & Competency Ecosystem" />

  <!-- ======= 1. HERO SECTION INTEGRATED WITH PARTNERS ======= -->
  <section class="w-full relative pt-12 pb-10 sm:py-20 lg:pt-32 lg:pb-24 bg-radial from-slate-100 via-slate-50 to-slate-50 overflow-hidden border-b border-slate-200/50">
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-500/10 blur-3xl rounded-full pointer-events-none"></div>
    <div class="absolute bottom-10 left-1/3 w-96 h-96 bg-blue-600/10 blur-3xl rounded-full pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 text-center space-y-6 sm:space-y-8 relative z-10">
      <div class="inline-flex items-center space-x-2 px-3 py-1.5 bg-blue-50 border border-blue-200/50 rounded-full text-xs font-bold text-blue-600 tracking-wide uppercase select-none">
        <span>✨ Polibatam Online CV</span>
      </div>

      <h1 class="text-3xl sm:text-6xl font-black tracking-tight text-slate-950 max-w-4xl mx-auto leading-none">
        Digital Bridge Between <span class="text-blue-600 bg-clip-text">Vocational Talent</span> & Industry
      </h1>

      <p class="text-sm sm:text-xl text-slate-500 max-w-2xl mx-auto font-medium leading-relaxed">
        Tracing student performance competencies and simplifying corporate recruitment processes to promote jobs or internship vacancies to Politeknik Negeri Batam students.
      </p>

      <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="#internships" class="w-full sm:w-auto px-8 py-3.5 font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/20 rounded-2xl transition transform active:scale-98 text-center text-sm">
          Explore Openings
        </a>
        <a href="#students" class="w-full sm:w-auto px-8 py-3.5 font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl shadow-xs transition text-center text-sm">
          View Student Portfolios
        </a>

         
<Link
    :href="route('tu.login')"
    class="w-full sm:w-auto px-8 py-3.5 font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-2xl shadow-xs transition text-center text-sm"
>
    Dashboard TU →
</Link>

      </div>

      <!-- Supporting Partners -->
      <div class="pt-8 sm:pt-16 max-w-5xl mx-auto space-y-4">
        <p class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest">
          Supported & Integrated With Strategic Industries
        </p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 sm:gap-8 items-center justify-items-center opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
          <img src="/img/sponsor/ilo.png" class="max-h-8 sm:max-h-10 w-auto object-contain" alt="ILO Logo">
          <img src="/img/sponsor/polibatam.png" class="max-h-8 sm:max-h-10 w-auto object-contain" alt="Polibatam Logo">
          <img src="/img/sponsor/skillindonesia.jpg" class="max-h-8 sm:max-h-10 w-auto object-contain rounded-md" alt="Skills for Prosperity">
          <img src="/img/sponsor/bej.jpg" class="max-h-8 sm:max-h-10 w-auto object-contain rounded-md" alt="BEJ Group Logo">
        </div>
      </div>
    </div>
  </section>

  <!-- ======= 2. BENTO COUNTS STATS OVERVIEW ======= -->
  <section class="w-full py-12 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-6 bg-slate-50 border border-slate-200/60 rounded-2xl">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Registered Companies</span>
          <div class="text-3xl font-black mt-2 text-slate-950">{{ props.stats.companies }}</div>
        </div>
        <div class="p-6 bg-blue-600 text-white rounded-2xl shadow-xs">
          <span class="text-xs font-bold opacity-80 uppercase tracking-wider">Active Talents</span>
          <div class="text-3xl font-black mt-2">{{ props.stats.students }}</div>
        </div>
        <div class="p-6 bg-slate-50 border border-slate-200/60 rounded-2xl">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Intern Vacancies</span>
          <div class="text-2xl font-black mt-2 text-slate-900">{{ props.stats.interns }}</div>
        </div>
        <div class="p-6 bg-slate-50 border border-slate-200/60 rounded-2xl">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Job Openings</span>
          <div class="text-2xl font-black mt-2 text-blue-600">{{ props.stats.jobs }}</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ======= 3. INTERNSHIPS SECTIONS ======= -->
  <section id="internships" class="w-full py-16 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-6 space-y-10">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="space-y-1">
          <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Featured Opportunities</h3>
          <p class="text-xs text-slate-400 font-medium">Exclusive vacancy slots curated specifically for Polibatam academic tracks.</p>
        </div>
      </div>

      <!-- TAMPILKAN GRID JIKA DATA TERSEDIA -->
      <div v-if="internOffers.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="item in internOffers" :key="item.id" class="p-6 bg-slate-50/50 border border-slate-200/60 hover:border-blue-600/30 rounded-3xl transition flex flex-col justify-between group">
          <div class="space-y-3.5">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-wide truncate max-w-[70%]">{{ item.name }}</span>
              <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-[9px] font-extrabold uppercase tracking-wider">{{ item.category }}</span>
            </div>
            <h4 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ item.position }}</h4>
            <p class="text-xs text-slate-500 font-medium leading-relaxed line-clamp-3">{{ item.description }}</p>
          </div>
          <div class="pt-4 mt-6 border-t border-slate-200/40 flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-400">Apply Open</span>
            <span class="text-xs text-blue-600 font-extrabold cursor-pointer group-hover:underline">Read More &rarr;</span>
          </div>
        </div>
      </div>

      <!-- KONDISI V-ELSE JIKA DATA LOWONGAN KOSONG -->
      <div v-else class="text-center py-12 border border-dashed border-slate-200 rounded-3xl bg-slate-50/30 text-slate-400 text-xs font-medium">
        🔒 No active internship or job openings available at the moment.
      </div>
    </div>
  </section>

  <!-- ======= 4. OUR STUDENTS SECTION (DILIMIT MAKSIMAL 10 MAHASISWA) ======= -->
  <section id="students" class="w-full py-16 bg-slate-50/50 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-6 space-y-10">
      <div class="text-center max-w-xl mx-auto space-y-2">
        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block">Talent Pool</span>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Meet Our Outstanding Students</h2>
      </div>

      <div v-if="props.students.length > 0" class="grid grid-cols-2 md:grid-cols-5 gap-6">
        <div v-for="std in props.students" :key="std.id_mhs" class="bg-white border border-slate-200/50 rounded-2xl p-4 text-center space-y-3 hover:shadow-xs transition">
          <div class="h-16 w-16 rounded-full mx-auto bg-blue-50 flex items-center justify-center font-black text-blue-600 text-sm border border-blue-100">
            {{ std.name.substring(0, 2).toUpperCase() }}
          </div>
          <div>
            <h4 class="text-xs font-black text-slate-900 truncate" :title="std.name">{{ std.name }}</h4>
            <p class="text-[10px] text-slate-400 font-bold truncate mt-0.5">{{ std.prodi }}</p>
          </div>
          <Link :href="route('students.show', { id_mhs: std.id_mhs })" class="text-[10px] bg-slate-50 text-slate-600 hover:text-blue-600 py-1.5 rounded-lg font-bold block transition">
            View Profile
          </Link>
        </div>
      </div>

      <div v-else class="text-center py-6 text-xs font-medium text-slate-400">
        No active student portfolios available at the moment.
      </div>

      <!-- Notifikasi Direktori Siswa -->
      <div class="bg-blue-50 border border-blue-100/60 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 max-w-3xl mx-auto text-center sm:text-left">
        <div>
          <h4 class="text-xs font-black text-blue-900 uppercase tracking-wide">Looking for other talented candidates?</h4>
          <p class="text-[11px] text-blue-700 font-medium mt-0.5">Explore our comprehensive directory filter tabs to sort potentials across all engineering departments.</p>
        </div>
        <Link :href="route('students.index')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition whitespace-nowrap shrink-0 shadow-xs">
          Browse All Students &rarr;
        </Link>
      </div>
    </div>
  </section>

  <!-- ======= 5. FREQUENTLY ASKED QUESTIONS (FAQ) ======= -->
  <section id="faq" class="w-full py-16 bg-white border-b border-slate-100">
    <div class="max-w-3xl mx-auto px-6 space-y-8">
      <div class="text-center space-y-1.5">
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight uppercase">F.A.Q</h2>
        <p class="text-xs text-slate-400 font-medium">Frequently Asked Questions Regarding TalentHub Access.</p>
      </div>

      <div class="space-y-2">
        <!-- FAQ 1 -->
        <div class="border border-slate-200/70 rounded-2xl overflow-hidden bg-slate-50/50">
          <button @click="toggleFaq(1)" class="w-full px-5 py-4 text-left text-xs sm:text-sm font-bold text-slate-900 flex items-center justify-between focus:outline-hidden">
            <span>How to register for an internship?</span>
            <span class="text-lg text-slate-400">{{ activeFaq === 1 ? '−' : '+' }}</span>
          </button>
          <div v-if="activeFaq === 1" class="px-5 pb-4 text-xs text-slate-500 font-medium leading-relaxed border-t border-slate-200/40 pt-2 animate-fadeIn">
            Please log in first using your official Polibatam student SSO account, navigate to the specific internship vacancy post you wish to pursue, verify your portfolio fields, and click submit.
          </div>
        </div>

        <!-- FAQ 2 -->
        <div class="border border-slate-200/70 rounded-2xl overflow-hidden bg-slate-50/50">
          <button @click="toggleFaq(2)" class="w-full px-5 py-4 text-left text-xs sm:text-sm font-bold text-slate-900 flex items-center justify-between focus:outline-hidden">
            <span>How to log in to Polibatam TalentHub?</span>
            <span class="text-lg text-slate-400">{{ activeFaq === 2 ? '−' : '+' }}</span>
          </button>
          <div v-if="activeFaq === 2" class="px-5 pb-4 text-xs text-slate-500 font-medium leading-relaxed border-t border-slate-200/40 pt-2 animate-fadeIn">
            Students must use their Single Sign-On credentials. Corporate human resource divisions can initiate credential requests by registering through our industry partner gateway portal.
          </div>
        </div>

        <!-- FAQ 3 -->
        <div class="border border-slate-200/70 rounded-2xl overflow-hidden bg-slate-50/50">
          <button @click="toggleFaq(3)" class="w-full px-5 py-4 text-left text-xs sm:text-sm font-bold text-slate-900 flex items-center justify-between focus:outline-hidden">
            <span>How to upload verified portfolio data?</span>
            <span class="text-lg text-slate-400">{{ activeFaq === 3 ? '−' : '+' }}</span>
          </button>
          <div v-if="activeFaq === 3" class="px-5 pb-4 text-xs text-slate-500 font-medium leading-relaxed border-t border-slate-200/40 pt-2 animate-fadeIn">
            Access your secure Student Dashboard workspace area, open any sub-item management tabs (such as Academic, Related Achievements, or Competency), and update the dynamic entry grids.
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ======= 6. MAPS INTEGRATION & INDUSTRIAL GATEWAY ======= -->
<!-- ======= SECTION KONTAK & LOKASI PETA (CLEAN LIGHT-THEME FIXED) ======= -->
  <section id="kontak" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-slate-200/60 tracking-tight">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

          <!-- Kolom Kiri: Informasi Alamat & Kontak Kontras -->
          <div class="space-y-6">
              <div>
                  <h2 class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">Contact Us</h2>
                  <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Industrial Gateway</p>
              </div>

              <div class="space-y-4">
                  <!-- Alamat -->
                  <div class="flex gap-4 p-4 bg-slate-50 border border-slate-200/60 rounded-2xl shadow-xs">
                    <span class="text-xl mt-0.5 select-none">📍</span>
                    <div>
                      <h4 class="font-bold text-sm text-slate-950">Campus Headquarters</h4>
                      <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                        Politeknik Negeri Batam, Batam Centre, Jl. Ahmad Yani, Kepulauan Riau, Indonesia
                      </p>
                    </div>
                  </div>

                  <!-- Telepon -->
                  <div class="flex gap-4 p-4 bg-slate-50 border border-slate-200/60 rounded-2xl shadow-xs">
                    <span class="text-xl mt-0.5 select-none">📞</span>
                    <div>
                      <h4 class="font-bold text-sm text-slate-950">Phone Channels</h4>
                      <p class="text-slate-500 text-xs mt-1">+62 778 469858</p>
                    </div>
                  </div>

                  <!-- Email -->
                  <div class="flex gap-4 p-4 bg-slate-50 border border-slate-200/60 rounded-2xl shadow-xs">
                    <span class="text-xl mt-0.5 select-none">✉️</span>
                    <div>
                      <h4 class="font-bold text-sm text-slate-950">Official Support Email</h4>
                      <p class="text-blue-600 text-xs mt-1 font-semibold hover:underline cursor-pointer">talenthub@polibatam.ac.id</p>
                    </div>
                  </div>
              </div>
          </div>

          <!-- Kolom Kanan: Peta Interaktif -->
          <div class="lg:col-span-2 w-full h-[360px] bg-slate-100 border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden relative group">
              <iframe
                  class="w-full h-full border-0 absolute inset-0 z-0 transition-all duration-500 filter grayscale contrast-125 brightness-90 group-hover:filter-none"
                  src="https://maps.google.com/maps?q=Politeknik%20Negeri%20Batam&t=&z=16&ie=UTF8&iwloc=&output=embed"
                  allowfullscreen=""
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
              ></iframe>

              <div class="absolute inset-0 z-10 flex flex-col items-center justify-center p-6 text-center bg-slate-950/40 backdrop-blur-[0.5px] opacity-100 group-hover:opacity-0 transition-opacity duration-500 pointer-events-none">
                  <span class="text-3xl mb-2 animate-bounce select-none">📍</span>
                  <h4 class="font-black text-sm text-white tracking-wide uppercase">TalentHub Operational Center</h4>
                  <p class="text-[11px] text-slate-200 font-medium max-w-xs mt-1">
                    Connected Live to Batam Centre Industrial Free Trade Zone
                  </p>
              </div>
          </div>
      </div>

      <!-- Form Pesan Mandiri di Baris Bawah -->
      <div class="mt-12 max-w-4xl mx-auto border-t border-slate-100 pt-10">
        <form @submit.prevent="submitContact" class="bg-slate-50 border border-slate-200/60 p-6 sm:p-8 rounded-3xl space-y-5">
          <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <span>✉️</span> Platform Inquiry Dispatch Sheet
            </h3>
            <p class="text-[11px] text-slate-400 font-medium mt-1">
              Have system questions, technical bugs, or specialized corporate vacancy placement proposals? Send a direct query message to our platform administrators.
            </p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-slate-500 uppercase block">Your Name</label>
              <input v-model="contactForm.name" type="text" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:outline-hidden focus:border-blue-600 bg-white transition" placeholder="John Doe">
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-slate-500 uppercase block">Your Email</label>
              <input v-model="contactForm.email" type="email" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:outline-hidden focus:border-blue-600 bg-white transition" placeholder="johndoe@example.com">
            </div>
          </div>

          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase block">Subject Topic</label>
            <input v-model="contactForm.subject" type="text" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:outline-hidden focus:border-blue-600 bg-white transition" placeholder="Corporate Vacancy Partnership Inquiries">
          </div>

          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase block">Message Body</label>
            <textarea v-model="contactForm.message" rows="4" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:outline-hidden focus:border-blue-600 bg-white transition resize-none" placeholder="State your message core details here..."></textarea>
          </div>

          <div class="flex justify-end pt-2">
            <button type="submit" :disabled="contactForm.processing" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold rounded-xl text-xs transition cursor-pointer shadow-xs">
              {{ contactForm.processing ? 'Sending Dispatch...' : 'Send Message' }}
            </button>
          </div>
        </form>
      </div>
  </section>
</template>

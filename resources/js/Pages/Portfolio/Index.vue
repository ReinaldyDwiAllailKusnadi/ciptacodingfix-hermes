<template>
  <AdminLayout title="Manajemen Portofolio Web Publik">
    <div class="space-y-6">

      <!-- Action Bar -->
      <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs flex items-center justify-between gap-3">
        <div>
          <h2 class="font-extrabold text-sm sm:text-base text-[#08143a]">Katalog Showcase Portofolio</h2>
          <p class="text-xs text-[#495874]">Proyek yang tampil di halaman portofolio publik CiptaCoding</p>
        </div>

        <button
          @click="openModal(null)"
          class="inline-flex items-center gap-2 bg-[#0062ff] hover:bg-[#0051d4] text-white px-4 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all"
        >
          <span class="material-symbols-outlined text-[18px]">add_photo_alternate</span>
          <span>Tambah Portofolio</span>
        </button>
      </div>

      <!-- Portfolio Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="p in portfolios"
          :key="p.id"
          class="bg-white rounded-2xl border border-[#d2dced] shadow-xs overflow-hidden flex flex-col justify-between hover:border-[#0062ff] transition-all"
        >
          <div>
            <!-- Image Thumbnail -->
            <div class="relative h-44 w-full bg-slate-100 overflow-hidden">
              <img :src="'/' + p.image" :alt="p.title" class="w-full h-full object-cover" />
              <div class="absolute top-2.5 left-2.5">
                <span class="px-2.5 py-0.5 rounded-lg bg-white/95 text-[#0062ff] text-[11px] font-bold shadow-xs">
                  {{ p.badge_top?.text || 'Live Project' }}
                </span>
              </div>
              <div class="absolute bottom-2.5 right-2.5">
                <span class="px-2 py-0.5 rounded bg-[#08143a]/80 text-white font-mono text-[10px]">
                  {{ p.code }}
                </span>
              </div>
            </div>

            <!-- Content -->
            <div class="p-4 space-y-2">
              <div class="text-[11px] text-[#495874] font-medium">{{ p.category_label }}</div>
              <h3 class="font-bold text-sm text-[#08143a] leading-snug">{{ p.title }}</h3>
              <p class="text-xs text-[#495874] line-clamp-2">{{ p.description }}</p>

              <!-- Tech tags -->
              <div class="flex flex-wrap gap-1 pt-2">
                <span
                  v-for="t in p.tech_stack"
                  :key="t"
                  class="px-2 py-0.5 rounded bg-[#edf4ff] text-[#0062ff] font-mono text-[10px] font-semibold"
                >
                  {{ t }}
                </span>
              </div>
            </div>
          </div>

          <!-- Actions Footer -->
          <div class="p-4 pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-emerald-600 font-semibold flex items-center gap-1">
              <span class="material-symbols-outlined text-[14px]">check_circle</span>
              <span>{{ p.highlight || 'Teruji' }}</span>
            </span>

            <div class="flex items-center gap-1">
              <button @click="openModal(p)" class="p-1.5 text-[#0062ff] hover:bg-[#edf4ff] rounded-lg transition-colors">
                <span class="material-symbols-outlined text-[18px]">edit</span>
              </button>
              <button @click="deletePortfolio(p)" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-[18px]">delete</span>
              </button>
            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- MODAL: TAMBAH / EDIT PORTOFOLIO -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 p-6 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-[#d2dced] pb-3">
          <h3 class="font-extrabold text-base text-[#08143a]">
            {{ isEditing ? 'Edit Portofolio' : 'Tambah Portofolio Baru' }}
          </h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-3.5 text-xs">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
              <label class="block font-bold text-[#08143a] mb-1">Judul Proyek *</label>
              <input v-model="form.title" type="text" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Contoh: POS Multi-Outlet Inventory"/>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Kode *</label>
              <input v-model="form.code" type="text" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl font-mono text-[#0062ff] font-bold outline-none" placeholder="PRJ-POS-01"/>
            </div>
          </div>

          <div>
            <label class="block font-bold text-[#08143a] mb-1">Label Kategori (Teks Display)</label>
            <input v-model="form.category_label" type="text" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Sistem Bisnis & POS Kasir"/>
          </div>

          <div>
            <label class="block font-bold text-[#08143a] mb-1">Kategori Filter Web (Checklist)</label>
            <div class="flex flex-wrap gap-2">
              <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer">
                <input type="checkbox" value="bisnis" v-model="form.categories" class="rounded text-[#0062ff]" />
                <span>Bisnis</span>
              </label>
              <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer">
                <input type="checkbox" value="website" v-model="form.categories" class="rounded text-[#0062ff]" />
                <span>Website</span>
              </label>
              <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer">
                <input type="checkbox" value="mobile" v-model="form.categories" class="rounded text-[#0062ff]" />
                <span>Mobile</span>
              </label>
              <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer">
                <input type="checkbox" value="it-project" v-model="form.categories" class="rounded text-[#0062ff]" />
                <span>IT Project</span>
              </label>
            </div>
          </div>

          <div>
            <label class="block font-bold text-[#08143a] mb-1">Deskripsi Singkat</label>
            <textarea v-model="form.description" rows="2.5" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none"></textarea>
          </div>

          <div>
            <label class="block font-bold text-[#08143a] mb-1">Tech Stack (Koma dipisah)</label>
            <input v-model="techStackInput" type="text" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl font-mono outline-none" placeholder="Laravel 11, MySQL, Tailwind CSS"/>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Highlight Benefit</label>
              <input v-model="form.highlight" type="text" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl text-emerald-600 font-semibold outline-none" placeholder="3 Cabang Sinkron"/>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Badge Atas</label>
              <input v-model="badgeTopInput" type="text" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Aktif Digunakan Usaha"/>
            </div>
          </div>

          <div>
            <label class="block font-bold text-[#08143a] mb-1">Pilih Gambar Asset</label>
            <select v-model="form.image" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
              <option value="assets/images/pos-erp-multi-outlet.svg">POS ERP Multi-Outlet (SVG)</option>
              <option value="assets/images/web-app-architecture.svg">Web App Architecture (SVG)</option>
              <option value="assets/images/visualisasi-website-company-profile.jpg">Company Profile Web (JPG)</option>
              <option value="assets/images/inventory-management-purchasing-decision.jpg">Inventory Management (JPG)</option>
              <option value="assets/images/marketplace-produk-digital-payment.jpg">Marketplace Digital (JPG)</option>
              <option value="assets/images/ai-computer-vision-image.jpg">AI Computer Vision (JPG)</option>
            </select>
          </div>

          <div class="pt-3 border-t border-[#d2dced] flex justify-end gap-2">
            <button type="button" @click="showModal = false" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-xl">Batal</button>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-[#0062ff] hover:bg-[#0051d4] text-white font-bold rounded-xl shadow-md">
              {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Perbarui Portofolio' : 'Simpan Portofolio') }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  portfolios: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const techStackInput = ref('');
const badgeTopInput = ref('');

const form = useForm({
  title: '',
  code: '',
  category_label: '',
  categories: ['bisnis'],
  description: '',
  image: 'assets/images/pos-erp-multi-outlet.svg',
  badge_top: { text: 'Live Project', dot: true },
  badge_bottom: 'Digital Solution',
  tech_stack: [],
  highlight: '',
  cta_text: 'Tanya Spek',
  whatsapp_text: '',
});

function openModal(item = null) {
  if (item) {
    isEditing.value = true;
    editingId.value = item.id;
    form.title = item.title;
    form.code = item.code;
    form.category_label = item.category_label || '';
    form.categories = item.categories || ['bisnis'];
    form.description = item.description || '';
    form.image = item.image;
    form.highlight = item.highlight || '';
    form.cta_text = item.cta_text || 'Tanya Spek';
    form.whatsapp_text = item.whatsapp_text || '';
    techStackInput.value = (item.tech_stack || []).join(', ');
    badgeTopInput.value = item.badge_top?.text || 'Live Project';
  } else {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    techStackInput.value = 'Laravel, MySQL, Tailwind CSS';
    badgeTopInput.value = 'Live Project';
    form.code = `PRJ-${String(props.portfolios.length + 1).padStart(2, '0')}`;
  }
  showModal.value = true;
}

function submitForm() {
  form.tech_stack = techStackInput.value.split(',').map(s => s.trim()).filter(Boolean);
  form.badge_top = { text: badgeTopInput.value, dot: true };

  if (isEditing.value) {
    form.put(route('portfolio.update', editingId.value), {
      onSuccess: () => (showModal.value = false)
    });
  } else {
    form.post(route('portfolio.store'), {
      onSuccess: () => (showModal.value = false)
    });
  }
}

function deletePortfolio(p) {
  if (confirm(`Hapus portofolio "${p.title}"?`)) {
    router.delete(route('portfolio.destroy', p.id));
  }
}
</script>

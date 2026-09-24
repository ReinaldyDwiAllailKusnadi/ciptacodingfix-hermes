<template>
  <AdminLayout title="Manajemen Proyek IT & Software">
    <div class="space-y-6">

      <!-- Action & Filter Bar -->
      <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs">
          <Link
            :href="route('projects.index', { status: 'all' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_status === 'all' ? 'bg-[#0062ff] text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Semua ({{ projects.length }})
          </Link>
          <Link
            :href="route('projects.index', { status: 'deal_dp' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_status === 'deal_dp' ? 'bg-[#0062ff] text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Deal &amp; DP
          </Link>
          <Link
            :href="route('projects.index', { status: 'in_progress' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_status === 'in_progress' ? 'bg-[#0062ff] text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Sedang Dikerjakan
          </Link>
          <Link
            :href="route('projects.index', { status: 'review' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_status === 'review' ? 'bg-[#0062ff] text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Review / UAT
          </Link>
          <Link
            :href="route('projects.index', { status: 'completed' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_status === 'completed' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Selesai
          </Link>
        </div>

        <!-- Add Project Button -->
        <button
          @click="openModal(null)"
          class="inline-flex items-center justify-center gap-2 bg-[#0062ff] hover:bg-[#0051d4] text-white px-4 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all"
        >
          <span class="material-symbols-outlined text-[18px]">add_circle</span>
          <span>Daftarkan Proyek Baru</span>
        </button>
      </div>

      <!-- Project Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="p in projects"
          :key="p.id"
          class="bg-white rounded-2xl border border-[#d2dced] shadow-xs p-5 flex flex-col justify-between space-y-4 hover:border-[#0062ff] transition-all"
        >
          <!-- Card Header -->
          <div>
            <div class="flex items-center justify-between text-xs mb-2">
              <span class="font-mono font-bold text-[#0062ff] bg-[#edf4ff] px-2 py-0.5 rounded-md">
                {{ p.code }}
              </span>
              <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider', getStatusBadge(p.status)]">
                {{ formatStatus(p.status) }}
              </span>
            </div>

            <h3 class="font-bold text-base text-[#08143a] leading-snug">{{ p.title }}</h3>
            <p class="text-xs text-[#495874] mt-1 line-clamp-2">{{ p.description || '-' }}</p>
          </div>

          <!-- Client & Financial Info -->
          <div class="p-3 bg-slate-50 rounded-xl space-y-2 text-xs">
            <div class="flex items-center justify-between">
              <span class="text-slate-400">Klien:</span>
              <span class="font-bold text-[#08143a]">{{ p.client?.name }} ({{ p.client?.company || '-' }})</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-400">Total Budget:</span>
              <span class="font-extrabold text-[#08143a]">{{ formatRupiah(p.total_budget) }}</span>
            </div>
            <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-200">
              <span class="text-emerald-600 font-semibold">Dibayar: {{ formatRupiah(p.paid_amount) }}</span>
              <span class="text-amber-600 font-semibold">Sisa: {{ formatRupiah(p.remaining_amount) }}</span>
            </div>
          </div>

          <!-- Progress Bar & Deadline -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between text-xs">
              <span class="text-[#495874] font-medium">Progress Pengerjaan</span>
              <span class="font-bold text-[#0062ff]">{{ p.progress_percent }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
              <div class="bg-[#0062ff] h-full rounded-full transition-all duration-500" :style="{ width: p.progress_percent + '%' }"></div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-[#495874] pt-1">
              <span>Mulai: {{ p.start_date || '-' }}</span>
              <span class="font-semibold text-red-600">Deadline: {{ p.deadline || '-' }}</span>
            </div>
          </div>

          <!-- Card Actions -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
            <a
              v-if="p.client?.whatsapp"
              :href="`https://wa.me/${p.client.whatsapp.replace(/^0/, '62')}?text=${encodeURIComponent('Halo Kak ' + p.client.name + ', update progress project ' + p.title)}`"
              target="_blank"
              class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1"
            >
              <span class="material-symbols-outlined text-[16px]">chat</span>
              <span>Hubungi Klien</span>
            </a>
            <span v-else></span>

            <div class="flex items-center gap-1">
              <button @click="openModal(p)" class="p-1.5 text-[#0062ff] hover:bg-[#edf4ff] rounded-lg transition-colors">
                <span class="material-symbols-outlined text-[18px]">edit</span>
              </button>
              <button @click="deleteProject(p)" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-[18px]">delete</span>
              </button>
            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- MODAL: TAMBAH / EDIT PROYEK -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 p-6 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-[#d2dced] pb-3">
          <h3 class="font-extrabold text-base text-[#08143a]">
            {{ isEditing ? 'Edit Data Proyek' : 'Daftarkan Proyek Baru' }}
          </h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-3.5 text-xs">
          <!-- Klien & Kode -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Pilih Klien *</label>
              <select v-model="form.client_id" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
                <option value="" disabled>-- Pilih Klien --</option>
                <option v-for="c in clients" :key="c.id" :value="c.id">
                  {{ c.name }} ({{ c.company || 'Personal' }})
                </option>
              </select>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Kode Proyek *</label>
              <input v-model="form.code" type="text" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl font-mono text-[#0062ff] font-bold outline-none" placeholder="PRJ-2026-XX"/>
            </div>
          </div>

          <!-- Judul Proyek -->
          <div>
            <label class="block font-bold text-[#08143a] mb-1">Nama / Judul Proyek *</label>
            <input v-model="form.title" type="text" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Contoh: Sistem POS Kasir & Inventaris Multi-Outlet"/>
          </div>

          <!-- Kategori & Status -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Kategori Sistem *</label>
              <select v-model="form.category" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
                <option value="website">Website Bisnis &amp; Company Profile</option>
                <option value="pos_erp">Aplikasi Kasir POS &amp; ERP</option>
                <option value="mobile_app">Aplikasi Mobile Android / iOS</option>
                <option value="custom_software">Custom Software &amp; Sistem Internal</option>
                <option value="it_consulting">Integrasi API &amp; Maintenance</option>
              </select>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Status Proyek *</label>
              <select v-model="form.status" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
                <option value="lead">Lead / Diskusi Penawaran</option>
                <option value="deal_dp">Deal &amp; DP Diterima</option>
                <option value="in_progress">Sedang Dikerjakan (Development)</option>
                <option value="review">Review Klien / Testing UAT</option>
                <option value="completed">Selesai &amp; Serah Terima</option>
                <option value="cancelled">Dibatalkan</option>
              </select>
            </div>
          </div>

          <!-- Budget & Progress -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Total Nilai Proyek (Rp) *</label>
              <input v-model.number="form.total_budget" type="number" min="0" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl font-mono text-sm font-bold text-[#08143a] outline-none"/>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Progress Pengerjaan ({{ form.progress_percent }}%)</label>
              <input v-model.number="form.progress_percent" type="range" min="0" max="100" class="w-full mt-2 accent-[#0062ff]"/>
            </div>
          </div>

          <!-- Tanggal Mulai & Deadline -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Tanggal Mulai</label>
              <input v-model="form.start_date" type="date" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none"/>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Target Deadline</label>
              <input v-model="form.deadline" type="date" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none"/>
            </div>
          </div>

          <!-- Deskripsi -->
          <div>
            <label class="block font-bold text-[#08143a] mb-1">Deskripsi &amp; Lingkup Pekerjaan</label>
            <textarea v-model="form.description" rows="3" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Rincian modul atau fitur sistem..."></textarea>
          </div>

          <!-- Buttons -->
          <div class="pt-3 border-t border-[#d2dced] flex justify-end gap-2">
            <button type="button" @click="showModal = false" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-xl">Batal</button>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-[#0062ff] hover:bg-[#0051d4] text-white font-bold rounded-xl shadow-md">
              {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Perbarui Proyek' : 'Simpan Proyek') }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  projects: Array,
  clients: Array,
  current_status: String,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
  client_id: '',
  code: '',
  title: '',
  category: 'website',
  status: 'deal_dp',
  total_budget: 0,
  start_date: new Date().toISOString().substring(0, 10),
  deadline: '',
  progress_percent: 0,
  description: '',
});

function formatRupiah(num) {
  return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatStatus(status) {
  const map = {
    lead: 'Lead / Diskusi',
    deal_dp: 'Deal & DP',
    in_progress: 'Development',
    review: 'Review / UAT',
    completed: 'Selesai',
    cancelled: 'Batal',
  };
  return map[status] || status;
}

function getStatusBadge(status) {
  const map = {
    lead: 'bg-slate-100 text-slate-700',
    deal_dp: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-indigo-100 text-indigo-800',
    review: 'bg-amber-100 text-amber-800',
    completed: 'bg-emerald-100 text-emerald-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return map[status] || 'bg-slate-100 text-slate-700';
}

function openModal(project = null) {
  if (project) {
    isEditing.value = true;
    editingId.value = project.id;
    form.client_id = project.client ? project.client.id : '';
    form.code = project.code;
    form.title = project.title;
    form.category = project.category;
    form.status = project.status;
    form.total_budget = project.total_budget;
    form.start_date = project.start_date || '';
    form.deadline = project.deadline || '';
    form.progress_percent = project.progress_percent;
    form.description = project.description || '';
  } else {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.code = `PRJ-${new Date().getFullYear()}-${String(props.projects.length + 1).padStart(2, '0')}`;
    form.start_date = new Date().toISOString().substring(0, 10);
  }
  showModal.value = true;
}

function submitForm() {
  if (isEditing.value) {
    form.put(route('projects.update', editingId.value), {
      onSuccess: () => (showModal.value = false)
    });
  } else {
    form.post(route('projects.store'), {
      onSuccess: () => (showModal.value = false)
    });
  }
}

function deleteProject(p) {
  if (confirm(`Hapus proyek "${p.title}"? Seluruh data invoice terkait proyek ini akan ikut terhapus.`)) {
    router.delete(route('projects.destroy', p.id));
  }
}
</script>

<template>
  <AdminLayout title="Catatan Pengeluaran (Biaya Operasional)">
    <div class="space-y-6">

      <!-- Category Summary Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="text-[11px] text-[#495874] font-semibold">Total Biaya</div>
          <div class="text-base sm:text-lg font-extrabold text-red-600 mt-0.5">
            {{ formatRupiah(summary.total) }}
          </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="text-[11px] text-[#495874] font-semibold">Server &amp; Cloud</div>
          <div class="text-sm sm:text-base font-bold text-[#08143a] mt-0.5">
            {{ formatRupiah(summary.server_cloud) }}
          </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="text-[11px] text-[#495874] font-semibold">Fee Freelance</div>
          <div class="text-sm sm:text-base font-bold text-[#08143a] mt-0.5">
            {{ formatRupiah(summary.freelance_salary) }}
          </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="text-[11px] text-[#495874] font-semibold">Tools &amp; Lisensi</div>
          <div class="text-sm sm:text-base font-bold text-[#08143a] mt-0.5">
            {{ formatRupiah(summary.tools_licenses) }}
          </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="text-[11px] text-[#495874] font-semibold">Operasional</div>
          <div class="text-sm sm:text-base font-bold text-[#08143a] mt-0.5">
            {{ formatRupiah(summary.operational) }}
          </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="text-[11px] text-[#495874] font-semibold">Marketing &amp; Ads</div>
          <div class="text-sm sm:text-base font-bold text-[#08143a] mt-0.5">
            {{ formatRupiah(summary.marketing) }}
          </div>
        </div>
      </div>

      <!-- Action & Filter Bar -->
      <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <!-- Filter Category Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs">
          <Link
            :href="route('finance.expenses', { category: 'all' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_category === 'all' ? 'bg-[#0062ff] text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Semua
          </Link>
          <Link
            :href="route('finance.expenses', { category: 'server_cloud' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_category === 'server_cloud' ? 'bg-[#0062ff] text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Server &amp; Cloud
          </Link>
          <Link
            :href="route('finance.expenses', { category: 'freelance_salary' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_category === 'freelance_salary' ? 'bg-[#0062ff] text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Freelance &amp; Tim
          </Link>
          <Link
            :href="route('finance.expenses', { category: 'tools_licenses' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_category === 'tools_licenses' ? 'bg-[#0062ff] text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Tools
          </Link>
        </div>

        <!-- Add Expense Button -->
        <button
          @click="showCreateModal = true"
          class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all"
        >
          <span class="material-symbols-outlined text-[18px]">add_circle</span>
          <span>Catat Biaya Pengeluaran</span>
        </button>
      </div>

      <!-- Expenses Table -->
      <div class="bg-white rounded-2xl border border-[#d2dced] shadow-xs overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-slate-50/80 border-b border-[#d2dced] text-[#495874] font-bold text-xs uppercase tracking-wider">
              <tr>
                <th class="py-3.5 px-4">Tanggal</th>
                <th class="py-3.5 px-4">Keterangan Biaya</th>
                <th class="py-3.5 px-4">Kategori</th>
                <th class="py-3.5 px-4">Terkait Proyek</th>
                <th class="py-3.5 px-4 text-right">Nominal</th>
                <th class="py-3.5 px-4 text-right w-20">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="exp in expenses.data" :key="exp.id" class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 font-mono text-xs text-slate-500 whitespace-nowrap">
                  {{ exp.expense_date }}
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-bold text-[#08143a]">{{ exp.title }}</div>
                  <div class="text-[11px] text-slate-400" v-if="exp.notes">{{ exp.notes }}</div>
                </td>
                <td class="py-3.5 px-4">
                  <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-[#495874] font-semibold text-[11px]">
                    {{ formatCategory(exp.category) }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-xs text-blue-600 font-medium">
                  {{ exp.project ? `[${exp.project.code}] ${exp.project.title}` : '-' }}
                </td>
                <td class="py-3.5 px-4 text-right font-bold text-red-600 text-sm whitespace-nowrap">
                  - {{ formatRupiah(exp.amount) }}
                </td>
                <td class="py-3.5 px-4 text-right">
                  <button
                    @click="deleteExpense(exp)"
                    title="Hapus"
                    class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                  >
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- MODAL: CATAT PENGELUARAN BARU -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-[#d2dced] pb-3">
          <h3 class="font-extrabold text-base text-[#08143a]">Catat Biaya Pengeluaran Baru</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <form @submit.prevent="submitCreateExpense" class="space-y-3.5 text-xs">
          <div>
            <label class="block font-bold text-[#08143a] mb-1">Judul / Deskripsi Pengeluaran *</label>
            <input v-model="form.title" type="text" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Contoh: Sewa VPS Server LiteSpeed 1 Tahun"/>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Kategori Biaya *</label>
              <select v-model="form.category" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
                <option value="server_cloud">Server &amp; Cloud Hosting</option>
                <option value="freelance_salary">Fee Freelance &amp; Tim Dev</option>
                <option value="tools_licenses">Software Tools &amp; Lisensi</option>
                <option value="operational">Operasional &amp; Internet</option>
                <option value="marketing">Marketing, Ads &amp; Iklan</option>
              </select>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Nominal Biaya (Rp) *</label>
              <input v-model.number="form.amount" type="number" min="1" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl font-mono text-sm font-bold text-red-600 outline-none"/>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Tanggal Pengeluaran *</label>
              <input v-model="form.expense_date" type="date" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none"/>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Alokasi Proyek (Opsional)</label>
              <select v-model="form.project_id" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
                <option value="">-- Biaya Umum Studio --</option>
                <option v-for="p in projects" :key="p.id" :value="p.id">
                  [{{ p.code }}] {{ p.title }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-bold text-[#08143a] mb-1">Catatan Tambahan</label>
            <textarea v-model="form.notes" rows="2" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Catatan bukti transfer atau nama vendor..."></textarea>
          </div>

          <div class="pt-3 border-t border-[#d2dced] flex justify-end gap-2">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-xl">Batal</button>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-md">
              {{ form.processing ? 'Menyimpan...' : 'Simpan Biaya' }}
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
  expenses: Object,
  projects: Array,
  summary: Object,
  current_category: String,
});

const showCreateModal = ref(false);

const form = useForm({
  title: '',
  category: 'operational',
  amount: 0,
  expense_date: new Date().toISOString().substring(0, 10),
  project_id: '',
  notes: '',
});

function formatRupiah(num) {
  return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatCategory(cat) {
  const map = {
    server_cloud: 'Server & Cloud',
    freelance_salary: 'Freelance & Tim',
    tools_licenses: 'Tools & Lisensi',
    operational: 'Operasional',
    marketing: 'Marketing & Ads',
  };
  return map[cat] || cat;
}

function submitCreateExpense() {
  form.post(route('finance.expenses.store'), {
    onSuccess: () => {
      showCreateModal.value = false;
      form.reset();
    }
  });
}

function deleteExpense(exp) {
  if (confirm(`Hapus catatan pengeluaran "${exp.title}"?`)) {
    router.delete(route('finance.expenses.destroy', exp.id));
  }
}
</script>

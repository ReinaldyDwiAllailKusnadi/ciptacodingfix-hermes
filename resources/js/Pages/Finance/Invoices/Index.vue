<template>
  <AdminLayout title="Manajemen Invoice & Tagihan">
    <div class="space-y-6">
      
      <!-- Financial Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs flex items-center justify-between">
          <div>
            <div class="text-xs text-[#495874] font-semibold">Total Tagihan Lunas</div>
            <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-1">
              {{ formatRupiah(summary.total_paid) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">{{ summary.count_paid }} Invoice</div>
          </div>
          <span class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <span class="material-symbols-outlined text-[24px]">verified</span>
          </span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs flex items-center justify-between">
          <div>
            <div class="text-xs text-[#495874] font-semibold">Piutang Belum Lunas</div>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-600 mt-1">
              {{ formatRupiah(summary.total_unpaid) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">{{ summary.count_unpaid }} Invoice Pending</div>
          </div>
          <span class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
            <span class="material-symbols-outlined text-[24px]">schedule</span>
          </span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs flex items-center justify-between">
          <div>
            <div class="text-xs text-[#495874] font-semibold">Total Keseluruhan Invoice</div>
            <div class="text-xl sm:text-2xl font-extrabold text-[#08143a] mt-1">
              {{ formatRupiah(summary.total_paid + summary.total_unpaid) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">{{ summary.count_all }} Transaksi</div>
          </div>
          <span class="w-12 h-12 rounded-xl bg-blue-50 text-[#0062ff] flex items-center justify-center">
            <span class="material-symbols-outlined text-[24px]">account_balance</span>
          </span>
        </div>
      </div>

      <!-- Action & Filter Bar -->
      <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs">
          <Link
            :href="route('finance.invoices', { status: 'all' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_status === 'all' ? 'bg-[#0062ff] text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Semua ({{ summary.count_all }})
          </Link>
          <Link
            :href="route('finance.invoices', { status: 'unpaid' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_status === 'unpaid' ? 'bg-amber-500 text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Belum Lunas ({{ summary.count_unpaid }})
          </Link>
          <Link
            :href="route('finance.invoices', { status: 'paid' })"
            :class="[
              'px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all',
              current_status === 'paid' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-[#495874] hover:bg-slate-100'
            ]"
          >
            Lunas ({{ summary.count_paid }})
          </Link>
        </div>

        <!-- Add Invoice Button -->
        <button
          @click="openCreateModal"
          class="inline-flex items-center justify-center gap-2 bg-[#0062ff] hover:bg-[#0051d4] text-white px-4 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all"
        >
          <span class="material-symbols-outlined text-[18px]">add</span>
          <span>Buat Invoice Baru</span>
        </button>
      </div>

      <!-- Invoices Table -->
      <div class="bg-white rounded-2xl border border-[#d2dced] shadow-xs overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-slate-50/80 border-b border-[#d2dced] text-[#495874] font-bold text-xs uppercase tracking-wider">
              <tr>
                <th class="py-3.5 px-4">No. Invoice</th>
                <th class="py-3.5 px-4">Klien &amp; Proyek</th>
                <th class="py-3.5 px-4">Tipe Tagihan</th>
                <th class="py-3.5 px-4">Nominal</th>
                <th class="py-3.5 px-4">Jatuh Tempo</th>
                <th class="py-3.5 px-4">Status</th>
                <th class="py-3.5 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-slate-50/70 transition-colors">
                <!-- No. Invoice -->
                <td class="py-3.5 px-4">
                  <div class="font-mono font-bold text-[#0062ff]">{{ inv.invoice_number }}</div>
                  <div class="text-[11px] text-slate-400">{{ inv.issue_date }}</div>
                </td>

                <!-- Klien & Proyek -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-[#08143a]">{{ inv.client?.name }}</div>
                  <div class="text-[11px] text-[#495874]">{{ inv.client?.company || '-' }}</div>
                  <div class="text-[11px] text-blue-600 font-medium line-clamp-1" v-if="inv.project">
                    {{ inv.project.title }}
                  </div>
                </td>

                <!-- Tipe Tagihan -->
                <td class="py-3.5 px-4">
                  <span class="px-2 py-0.5 rounded-md bg-[#edf4ff] text-[#0062ff] font-mono text-[11px] font-semibold">
                    {{ formatInvoiceType(inv.type) }}
                  </span>
                </td>

                <!-- Nominal -->
                <td class="py-3.5 px-4">
                  <div class="font-extrabold text-[#08143a]">{{ formatRupiah(inv.amount) }}</div>
                  <div class="text-[10px] text-slate-400 uppercase" v-if="inv.payment_method">{{ inv.payment_method.replace('_', ' ') }}</div>
                </td>

                <!-- Jatuh Tempo -->
                <td class="py-3.5 px-4">
                  <div class="text-xs text-[#08143a]">{{ inv.due_date }}</div>
                  <div v-if="inv.paid_at" class="text-[10px] text-emerald-600 font-semibold">
                    Dibayar: {{ inv.paid_at }}
                  </div>
                </td>

                <!-- Status -->
                <td class="py-3.5 px-4">
                  <span
                    :class="[
                      'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider',
                      inv.status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                    ]"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="inv.status === 'paid' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                    {{ inv.status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Preview Invoice Modal -->
                    <button
                      @click="previewInvoice(inv)"
                      title="Lihat Faktur / Cetak"
                      class="p-1.5 text-[#0062ff] hover:bg-[#edf4ff] rounded-lg transition-colors"
                    >
                      <span class="material-symbols-outlined text-[18px]">print</span>
                    </button>

                    <!-- Toggle Status Lunas -->
                    <button
                      v-if="inv.status !== 'paid'"
                      @click="markAsPaid(inv)"
                      title="Tandai Sudah Lunas"
                      class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                    >
                      <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    </button>

                    <!-- WhatsApp Reminder Link -->
                    <a
                      v-if="inv.client?.whatsapp && inv.status !== 'paid'"
                      :href="generateWaReminder(inv)"
                      target="_blank"
                      title="Kirim Pengingat WhatsApp"
                      class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                    >
                      <span class="material-symbols-outlined text-[18px]">chat</span>
                    </a>

                    <!-- Delete -->
                    <button
                      @click="deleteInvoice(inv)"
                      title="Hapus Invoice"
                      class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                    >
                      <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- MODAL: BUAT INVOICE BARU -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-[#d2dced] pb-3">
          <h3 class="font-extrabold text-base text-[#08143a]">Buat Invoice Tagihan Baru</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <form @submit.prevent="submitCreateInvoice" class="space-y-3.5 text-xs">
          <!-- Pilih Klien -->
          <div>
            <label class="block font-bold text-[#08143a] mb-1">Pilih Klien *</label>
            <select v-model="form.client_id" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
              <option value="" disabled>-- Pilih Klien --</option>
              <option v-for="c in clients" :key="c.id" :value="c.id">
                {{ c.name }} ({{ c.company || 'Personal' }})
              </option>
            </select>
          </div>

          <!-- Pilih Proyek -->
          <div>
            <label class="block font-bold text-[#08143a] mb-1">Pilih Proyek (Opsional)</label>
            <select v-model="form.project_id" @change="onProjectSelect" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
              <option value="">-- Tanpa Proyek / Tagihan Lain --</option>
              <option v-for="p in projects" :key="p.id" :value="p.id">
                [{{ p.code }}] {{ p.title }} - (Budget: {{ formatRupiah(p.total_budget) }})
              </option>
            </select>
          </div>

          <!-- Tipe Tagihan & Nominal -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Tipe Tagihan</label>
              <select v-model="form.type" @change="onTypeChange" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
                <option value="dp_50">DP 50% (Uang Muka)</option>
                <option value="termin_1">Termin 1 (30%)</option>
                <option value="termin_2">Termin 2 (20%)</option>
                <option value="pelunasan">Pelunasan Akhir</option>
                <option value="full_payment">Pembayaran Penuh 100%</option>
                <option value="custom">Nominal Kustom</option>
              </select>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Nominal Tagihan (Rp) *</label>
              <input v-model.number="form.amount" type="number" min="1" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl font-mono text-sm font-bold text-[#08143a] outline-none"/>
            </div>
          </div>

          <!-- Tanggal Terbit & Jatuh Tempo -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Tanggal Terbit *</label>
              <input v-model="form.issue_date" type="date" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none"/>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Jatuh Tempo (Due Date) *</label>
              <input v-model="form.due_date" type="date" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none"/>
            </div>
          </div>

          <!-- Catatan / Instruksi Transfer -->
          <div>
            <label class="block font-bold text-[#08143a] mb-1">Catatan Pembayaran / Bank Tujuan</label>
            <textarea v-model="form.notes" rows="2" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Contoh: Transfer ke Rekening BCA 12345678 a.n CiptaCoding"></textarea>
          </div>

          <!-- Tombol Simpan -->
          <div class="pt-3 border-t border-[#d2dced] flex justify-end gap-2">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-xl">Batal</button>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-[#0062ff] hover:bg-[#0051d4] text-white font-bold rounded-xl shadow-md">
              {{ form.processing ? 'Menyimpan...' : 'Terbitkan Invoice' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL: PREVIEW & CETAK FAKTUR INVOICE -->
    <div v-if="selectedInvoice" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-100 p-8 space-y-6 max-h-[90vh] overflow-y-auto print:m-0 print:p-0">
        <!-- Invoice Header -->
        <div class="flex items-start justify-between border-b border-slate-200 pb-6">
          <div class="flex items-center gap-3">
            <img src="/assets/images/logo.png" alt="Logo" class="h-10 w-auto" />
            <div>
              <div class="font-extrabold text-xl text-[#08143a]">Cipta<span class="text-[#0062ff]">Coding</span></div>
              <div class="text-[11px] text-[#495874]">Digital Studio &amp; Software House</div>
              <div class="text-[11px] text-slate-400">WhatsApp: 0877-2305-7547 • info@ciptacoding.com</div>
            </div>
          </div>
          <div class="text-right">
            <div class="text-xl font-extrabold text-[#08143a] tracking-tight">INVOICE</div>
            <div class="font-mono text-xs font-bold text-[#0062ff] mt-0.5">{{ selectedInvoice.invoice_number }}</div>
            <span
              :class="[
                'inline-block mt-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                selectedInvoice.status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
              ]"
            >
              {{ selectedInvoice.status === 'paid' ? 'LUNAS (PAID)' : 'MENUNGGU PEMBAYARAN' }}
            </span>
          </div>
        </div>

        <!-- Invoice Details Row -->
        <div class="grid grid-cols-2 gap-4 text-xs">
          <div>
            <div class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mb-1">Ditagihkan Kepada:</div>
            <div class="font-bold text-sm text-[#08143a]">{{ selectedInvoice.client?.name }}</div>
            <div class="text-[#495874]">{{ selectedInvoice.client?.company || '-' }}</div>
            <div class="text-[#495874]">{{ selectedInvoice.client?.whatsapp || '-' }}</div>
          </div>
          <div class="text-right space-y-1">
            <div><span class="text-slate-400">Tanggal Terbit:</span> <span class="font-semibold text-[#08143a]">{{ selectedInvoice.issue_date }}</span></div>
            <div><span class="text-slate-400">Jatuh Tempo:</span> <span class="font-bold text-red-600">{{ selectedInvoice.due_date }}</span></div>
            <div v-if="selectedInvoice.paid_at"><span class="text-slate-400">Tanggal Lunas:</span> <span class="font-bold text-emerald-600">{{ selectedInvoice.paid_at }}</span></div>
          </div>
        </div>

        <!-- Line Items Table -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
          <table class="w-full text-xs">
            <thead class="bg-slate-50 font-bold text-[#495874] border-b border-slate-200">
              <tr>
                <th class="py-2.5 px-4 text-left">Deskripsi Layanan / Proyek</th>
                <th class="py-2.5 px-4 text-right">Nominal</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="py-3 px-4">
                  <div class="font-bold text-sm text-[#08143a]">
                    {{ selectedInvoice.project ? selectedInvoice.project.title : 'Tagihan Jasa IT & Pengembangan Software' }}
                  </div>
                  <div class="text-slate-500 text-[11px]">
                    Termin Pembayaran: {{ formatInvoiceType(selectedInvoice.type) }}
                  </div>
                </td>
                <td class="py-3 px-4 text-right font-bold text-sm text-[#08143a]">
                  {{ formatRupiah(selectedInvoice.amount) }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50/70 border-t border-slate-200 font-bold">
              <tr>
                <td class="py-3 px-4 text-right">TOTAL TAGIHAN:</td>
                <td class="py-3 px-4 text-right text-base text-[#0062ff]">{{ formatRupiah(selectedInvoice.amount) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Payment Instructions Note -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
          <div class="font-bold text-[#08143a]">Petunjuk Pembayaran:</div>
          <p class="text-slate-600">
            {{ selectedInvoice.notes || 'Pembayaran dapat ditransfer melalui rekening Bank BCA 1234567890 a.n CiptaCoding. Kirim bukti transfer via WhatsApp ke 0877-2305-7547.' }}
          </p>
        </div>

        <!-- Modal Action Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-200 print:hidden">
          <button @click="selectedInvoice = null" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 rounded-xl">
            Tutup
          </button>
          <div class="flex items-center gap-2">
            <button @click="printInvoice" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold shadow-xs">
              <span class="material-symbols-outlined text-[16px]">print</span>
              <span>Cetak / Simpan PDF</span>
            </button>
          </div>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  invoices: Object,
  clients: Array,
  projects: Array,
  summary: Object,
  current_status: String,
});

const showCreateModal = ref(false);
const selectedInvoice = ref(null);

const form = useForm({
  client_id: '',
  project_id: '',
  type: 'dp_50',
  amount: 0,
  issue_date: new Date().toISOString().substring(0, 10),
  due_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().substring(0, 10),
  notes: 'Transfer Bank BCA 12345678 a.n CiptaCoding',
});

function formatRupiah(num) {
  return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatInvoiceType(type) {
  const map = {
    dp_50: 'DP 50%',
    termin_1: 'Termin 1 (30%)',
    termin_2: 'Termin 2 (20%)',
    pelunasan: 'Pelunasan',
    full_payment: 'Pembayaran Penuh',
    custom: 'Kustom',
  };
  return map[type] || type;
}

function onProjectSelect() {
  const proj = props.projects.find(p => p.id === form.project_id);
  if (proj) {
    form.client_id = proj.client_id;
    if (form.type === 'dp_50') {
      form.amount = Math.round(proj.total_budget * 0.5);
    } else if (form.type === 'full_payment') {
      form.amount = proj.total_budget;
    }
  }
}

function onTypeChange() {
  const proj = props.projects.find(p => p.id === form.project_id);
  if (!proj) return;

  if (form.type === 'dp_50') form.amount = Math.round(proj.total_budget * 0.5);
  else if (form.type === 'termin_1') form.amount = Math.round(proj.total_budget * 0.3);
  else if (form.type === 'termin_2') form.amount = Math.round(proj.total_budget * 0.2);
  else if (form.type === 'pelunasan') form.amount = Math.round(proj.total_budget * 0.5);
  else if (form.type === 'full_payment') form.amount = proj.total_budget;
}

function openCreateModal() {
  form.reset();
  form.issue_date = new Date().toISOString().substring(0, 10);
  form.due_date = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().substring(0, 10);
  showCreateModal.value = true;
}

function submitCreateInvoice() {
  form.post(route('finance.invoices.store'), {
    onSuccess: () => {
      showCreateModal.value = false;
    }
  });
}

function previewInvoice(inv) {
  selectedInvoice.value = inv;
}

function printInvoice() {
  window.print();
}

function markAsPaid(inv) {
  if (confirm(`Tandai invoice ${inv.invoice_number} sebagai LUNAS?`)) {
    router.put(route('finance.invoices.update_status', inv.id), {
      status: 'paid',
      payment_method: 'transfer_bca'
    });
  }
}

function deleteInvoice(inv) {
  if (confirm(`Hapus invoice ${inv.invoice_number}?`)) {
    router.delete(route('finance.invoices.destroy', inv.id));
  }
}

function generateWaReminder(inv) {
  const phone = (inv.client?.whatsapp || '').replace(/^0/, '62').replace(/[^0-9]/g, '');
  const msg = `Halo Kak ${inv.client?.name}, mengingatkan untuk tagihan invoice CiptaCoding *#${inv.invoice_number}* sebesar *${formatRupiah(inv.amount)}* jatuh tempo pada *${inv.due_date}*. Terima kasih banyak!`;
  return `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
}
</script>

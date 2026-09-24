<template>
  <AdminLayout title="Dashboard Studio">
    <div class="space-y-6">
      
      <!-- Top Metric Cards (6 KPI Cards) -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pemasukan -->
        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="flex items-center justify-between text-[#495874] text-xs font-semibold mb-2">
            <span>Pemasukan Bulan Ini</span>
            <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
              <span class="material-symbols-outlined text-[18px]">trending_up</span>
            </span>
          </div>
          <div class="text-xl sm:text-2xl font-extrabold text-emerald-600">
            {{ formatRupiah(metrics.income_this_month) }}
          </div>
          <p class="text-[11px] text-[#495874] mt-1">Invoice lunas terbayar</p>
        </div>

        <!-- Pengeluaran -->
        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="flex items-center justify-between text-[#495874] text-xs font-semibold mb-2">
            <span>Pengeluaran Bulan Ini</span>
            <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
              <span class="material-symbols-outlined text-[18px]">trending_down</span>
            </span>
          </div>
          <div class="text-xl sm:text-2xl font-extrabold text-red-600">
            {{ formatRupiah(metrics.expenses_this_month) }}
          </div>
          <p class="text-[11px] text-[#495874] mt-1">Operasional & server</p>
        </div>

        <!-- Laba Bersih -->
        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="flex items-center justify-between text-[#495874] text-xs font-semibold mb-2">
            <span>Laba Bersih Bulan Ini</span>
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-[#0062ff] flex items-center justify-center">
              <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
            </span>
          </div>
          <div :class="['text-xl sm:text-2xl font-extrabold', metrics.net_profit_this_month >= 0 ? 'text-[#0062ff]' : 'text-red-500']">
            {{ formatRupiah(metrics.net_profit_this_month) }}
          </div>
          <p class="text-[11px] text-[#495874] mt-1">Net profit studio</p>
        </div>

        <!-- Piutang Belum Lunas -->
        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="flex items-center justify-between text-[#495874] text-xs font-semibold mb-2">
            <span>Piutang / Tagihan Pending</span>
            <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
              <span class="material-symbols-outlined text-[18px]">pending_actions</span>
            </span>
          </div>
          <div class="text-xl sm:text-2xl font-extrabold text-amber-600">
            {{ formatRupiah(metrics.unpaid_invoices_amount) }}
          </div>
          <p class="text-[11px] text-[#495874] mt-1">Menunggu pembayaran klien</p>
        </div>
      </div>

      <!-- Quick Action Bar -->
      <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <span class="font-extrabold text-sm text-[#08143a]">Aksi Cepat:</span>
          <span class="text-xs text-[#495874]">Buat invoice atau catat pengeluaran baru</span>
        </div>
        <div class="flex items-center gap-2">
          <Link
            :href="route('finance.invoices')"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#0062ff] hover:bg-[#0051d4] text-white rounded-xl text-xs font-bold shadow-xs transition-all"
          >
            <span class="material-symbols-outlined text-[16px]">add_circle</span>
            <span>Buat Invoice Tagihan</span>
          </Link>
          <Link
            :href="route('finance.expenses')"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-[#08143a] rounded-xl text-xs font-bold transition-all"
          >
            <span class="material-symbols-outlined text-[16px]">receipt</span>
            <span>Catat Pengeluaran</span>
          </Link>
          <Link
            :href="route('projects.index')"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-[#08143a] rounded-xl text-xs font-bold transition-all"
          >
            <span class="material-symbols-outlined text-[16px]">folder_open</span>
            <span>Daftar Proyek Baru</span>
          </Link>
        </div>
      </div>

      <!-- Cashflow Graph & Operational Stats Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cashflow 6-Month Chart -->
        <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="font-bold text-sm sm:text-base text-[#08143a]">Grafik Arus Kas 6 Bulan Terakhir</h2>
              <p class="text-xs text-[#495874]">Pemasukan vs Pengeluaran Riil (Cash Basis)</p>
            </div>
            <Link :href="route('finance.cashflow')" class="text-xs text-[#0062ff] font-bold hover:underline flex items-center gap-1">
              <span>Laporan Lengkap</span>
              <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </Link>
          </div>

          <!-- Bar Chart Container -->
          <div class="h-64 relative">
            <Bar :data="chartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Studio Quick Counters -->
        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs flex flex-col justify-between space-y-4">
          <div>
            <h2 class="font-bold text-sm sm:text-base text-[#08143a] mb-1">Status Operasional Studio</h2>
            <p class="text-xs text-[#495874]">Kapasitas dan klien aktif saat ini</p>
          </div>

          <div class="space-y-3">
            <div class="p-3.5 rounded-xl bg-[#edf4ff] border border-[#d2dced]/60 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-white text-[#0062ff] flex items-center justify-center shadow-2xs font-extrabold text-sm">
                  {{ metrics.active_projects_count }}
                </span>
                <div>
                  <div class="font-bold text-xs text-[#08143a]">Proyek Sedang Berjalan</div>
                  <div class="text-[11px] text-[#495874]">Tahap DP, Dev & Review</div>
                </div>
              </div>
              <Link :href="route('projects.index')" class="text-[#0062ff] hover:text-[#0051d4]">
                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
              </Link>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-[#d2dced]/60 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-white text-emerald-600 flex items-center justify-center shadow-2xs font-extrabold text-sm">
                  {{ metrics.total_clients_count }}
                </span>
                <div>
                  <div class="font-bold text-xs text-[#08143a]">Total Klien Terdaftar</div>
                  <div class="text-[11px] text-[#495874]">Buku kontak klien studio</div>
                </div>
              </div>
              <Link :href="route('clients.index')" class="text-[#0062ff] hover:text-[#0051d4]">
                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
              </Link>
            </div>
          </div>

          <!-- Quick Tip -->
          <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 flex items-start gap-2">
            <span class="material-symbols-outlined text-[16px] text-amber-600 mt-0.5 shrink-0">lightbulb</span>
            <span>Tagih pelunasan sisa termin sebelum rilis source code ke server production klien.</span>
          </div>
        </div>
      </div>

      <!-- Recent Invoices & Active Projects Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Invoices Terakhir -->
        <div class="bg-white rounded-2xl border border-[#d2dced] shadow-xs overflow-hidden">
          <div class="p-4 sm:p-5 border-b border-[#d2dced] flex items-center justify-between">
            <div>
              <h3 class="font-bold text-sm sm:text-base text-[#08143a]">Invoice Terbaru</h3>
              <p class="text-xs text-[#495874]">Faktur tagihan klien terakhir</p>
            </div>
            <Link :href="route('finance.invoices')" class="text-xs font-bold text-[#0062ff] hover:underline flex items-center gap-0.5">
              <span>Semua Invoice</span>
              <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </Link>
          </div>

          <div class="divide-y divide-slate-100 text-xs">
            <div v-for="inv in recent_invoices" :key="inv.id" class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
              <div class="space-y-0.5">
                <div class="font-mono font-bold text-[#0062ff]">{{ inv.invoice_number }}</div>
                <div class="font-bold text-[#08143a]">{{ inv.client?.name }} ({{ inv.client?.company || '-' }})</div>
                <div class="text-[11px] text-[#495874]">Due: {{ inv.due_date }}</div>
              </div>
              <div class="text-right space-y-1">
                <div class="font-bold text-sm text-[#08143a]">{{ formatRupiah(inv.amount) }}</div>
                <span
                  :class="[
                    'inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                    inv.status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                  ]"
                >
                  {{ inv.status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Proyek Berjalan -->
        <div class="bg-white rounded-2xl border border-[#d2dced] shadow-xs overflow-hidden">
          <div class="p-4 sm:p-5 border-b border-[#d2dced] flex items-center justify-between">
            <div>
              <h3 class="font-bold text-sm sm:text-base text-[#08143a]">Proyek Aktif</h3>
              <p class="text-xs text-[#495874]">Progress pengerjaan software & website</p>
            </div>
            <Link :href="route('projects.index')" class="text-xs font-bold text-[#0062ff] hover:underline flex items-center gap-0.5">
              <span>Semua Proyek</span>
              <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </Link>
          </div>

          <div class="divide-y divide-slate-100 text-xs">
            <div v-for="p in recent_projects" :key="p.id" class="p-4 space-y-2 hover:bg-slate-50 transition-colors">
              <div class="flex items-center justify-between">
                <div>
                  <span class="font-mono text-[10px] text-[#0062ff] font-bold">{{ p.code }}</span>
                  <div class="font-bold text-sm text-[#08143a]">{{ p.title }}</div>
                </div>
                <span class="text-xs font-bold text-[#495874]">{{ p.progress_percent }}%</span>
              </div>

              <!-- Progress bar -->
              <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-[#0062ff] h-full rounded-full transition-all duration-500" :style="{ width: p.progress_percent + '%' }"></div>
              </div>

              <div class="flex items-center justify-between text-[11px] text-[#495874]">
                <span>Klien: {{ p.client?.name }}</span>
                <span>Deadline: {{ p.deadline || '-' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
  metrics: Object,
  chart: Object,
  recent_invoices: Array,
  recent_projects: Array,
});

function formatRupiah(num) {
  return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

const chartData = computed(() => ({
  labels: props.chart?.labels || [],
  datasets: [
    {
      label: 'Pemasukan (Inflows)',
      backgroundColor: '#10b981',
      data: props.chart?.income || [],
      borderRadius: 6,
    },
    {
      label: 'Pengeluaran (Outflows)',
      backgroundColor: '#ef4444',
      data: props.chart?.expenses || [],
      borderRadius: 6,
    },
  ],
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
      labels: {
        font: { family: 'Plus Jakarta Sans', size: 11 },
      },
    },
  },
  scales: {
    x: { grid: { display: false } },
    y: {
      ticks: {
        callback: (val) => 'Rp ' + (val / 1000000) + ' Jt',
      },
    },
  },
};
</script>

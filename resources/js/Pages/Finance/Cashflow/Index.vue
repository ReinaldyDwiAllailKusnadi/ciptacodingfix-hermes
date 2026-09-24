<template>
  <AdminLayout title="Laporan Arus Kas & Laba Rugi">
    <div class="space-y-6">

      <!-- Annual Summary Header -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Pemasukan Tahunan -->
        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="text-xs text-[#495874] font-semibold">Total Pemasukan (Tahun {{ year }})</div>
          <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-1">
            {{ formatRupiah(total_income) }}
          </div>
          <p class="text-[11px] text-[#495874] mt-0.5">Akumulasi invoice lunas</p>
        </div>

        <!-- Pengeluaran Tahunan -->
        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="text-xs text-[#495874] font-semibold">Total Pengeluaran (Tahun {{ year }})</div>
          <div class="text-xl sm:text-2xl font-extrabold text-red-600 mt-1">
            {{ formatRupiah(total_expense) }}
          </div>
          <p class="text-[11px] text-[#495874] mt-0.5">Biaya server, freelance & operasional</p>
        </div>

        <!-- Net Profit -->
        <div class="bg-white p-5 rounded-2xl border border-[#d2dced] shadow-xs">
          <div class="text-xs text-[#495874] font-semibold">Laba Bersih Studio (Net Profit)</div>
          <div :class="['text-xl sm:text-2xl font-extrabold mt-1', total_profit >= 0 ? 'text-[#0062ff]' : 'text-red-500']">
            {{ formatRupiah(total_profit) }}
          </div>
          <p class="text-[11px] text-[#495874] mt-0.5">
            Margin Laba: <span class="font-bold">{{ total_income > 0 ? Math.round((total_profit / total_income) * 100) : 0 }}%</span>
          </p>
        </div>
      </div>

      <!-- 12-Month Cashflow Statement Table -->
      <div class="bg-white rounded-2xl border border-[#d2dced] shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#d2dced] flex items-center justify-between">
          <div>
            <h3 class="font-extrabold text-base text-[#08143a]">Buku Kas Bulanan (Tahun {{ year }})</h3>
            <p class="text-xs text-[#495874]">Rekapitulasi pergerakan arus kas bulanan CiptaCoding Studio</p>
          </div>
          <button @click="printReport" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-[#08143a] rounded-xl text-xs font-bold transition-all">
            <span class="material-symbols-outlined text-[16px]">print</span>
            <span>Cetak Laporan</span>
          </button>
        </div>

        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-slate-50/80 border-b border-[#d2dced] text-[#495874] font-bold text-xs uppercase tracking-wider">
              <tr>
                <th class="py-3 px-4">Bulan</th>
                <th class="py-3 px-4 text-right">Pemasukan (Inflow)</th>
                <th class="py-3 px-4 text-right">Pengeluaran (Outflow)</th>
                <th class="py-3 px-4 text-right">Laba / Rugi Bersih</th>
                <th class="py-3 px-4 text-right">Margin (%)</th>
                <th class="py-3 px-4 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="m in months" :key="m.month_number" class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3 px-4 font-bold text-[#08143a]">
                  {{ m.month_name }}
                </td>
                <td class="py-3 px-4 text-right font-mono font-semibold text-emerald-600">
                  {{ formatRupiah(m.income) }}
                </td>
                <td class="py-3 px-4 text-right font-mono font-semibold text-red-600">
                  {{ formatRupiah(m.expense) }}
                </td>
                <td :class="['py-3 px-4 text-right font-mono font-bold', m.profit >= 0 ? 'text-[#0062ff]' : 'text-red-500']">
                  {{ formatRupiah(m.profit) }}
                </td>
                <td class="py-3 px-4 text-right font-mono text-xs text-[#495874]">
                  {{ m.income > 0 ? Math.round((m.profit / m.income) * 100) + '%' : '-' }}
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    v-if="m.income > 0 || m.expense > 0"
                    :class="[
                      'inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                      m.profit >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                    ]"
                  >
                    {{ m.profit >= 0 ? 'Surplus' : 'Defisit' }}
                  </span>
                  <span v-else class="text-slate-300 text-xs">-</span>
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50/90 border-t-2 border-[#d2dced] font-extrabold text-xs sm:text-sm">
              <tr>
                <td class="py-4 px-4 text-[#08143a]">TOTAL TAHUNAN</td>
                <td class="py-4 px-4 text-right text-emerald-600 font-mono">{{ formatRupiah(total_income) }}</td>
                <td class="py-4 px-4 text-right text-red-600 font-mono">{{ formatRupiah(total_expense) }}</td>
                <td :class="['py-4 px-4 text-right font-mono', total_profit >= 0 ? 'text-[#0062ff]' : 'text-red-500']">
                  {{ formatRupiah(total_profit) }}
                </td>
                <td class="py-4 px-4 text-right font-mono">
                  {{ total_income > 0 ? Math.round((total_profit / total_income) * 100) + '%' : '0%' }}
                </td>
                <td class="py-4 px-4 text-center">
                  <span class="px-2.5 py-1 rounded-full bg-blue-100 text-[#0062ff] text-[10px] uppercase font-bold tracking-wider">
                    Audited
                  </span>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  year: Number,
  months: Array,
  total_income: Number,
  total_expense: Number,
  total_profit: Number,
});

function formatRupiah(num) {
  return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function printReport() {
  window.print();
}
</script>

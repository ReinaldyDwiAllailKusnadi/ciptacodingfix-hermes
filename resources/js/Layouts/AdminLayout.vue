<template>
  <div class="min-h-screen bg-[#f8faff] text-[#08143a] flex flex-col md:flex-row font-body selection:bg-[#0062ff] selection:text-white">
    <!-- Desktop Sidebar -->
    <aside class="w-64 bg-[#08143a] text-white flex-shrink-0 flex flex-col justify-between hidden md:flex border-r border-[#0a1744]">
      <div>
        <!-- Brand Header -->
        <div class="h-16 flex items-center gap-3 px-6 border-b border-white/10">
          <img src="/assets/images/logo.png" alt="Logo CiptaCoding" class="h-8 w-auto bg-white p-1 rounded-lg" />
          <div>
            <div class="font-extrabold text-base tracking-tight text-white">
              Cipta<span class="text-[#0062ff]">Coding</span>
            </div>
            <div class="text-[10px] text-blue-200 uppercase font-mono tracking-widest">
              Studio ERP v2.0
            </div>
          </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-1.5 text-xs">
          <!-- Main Group -->
          <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
            Utama
          </div>
          <Link
            :href="route('dashboard')"
            :class="[
              'flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all',
              route().current('dashboard')
                ? 'bg-[#0062ff] text-white shadow-md'
                : 'text-slate-300 hover:bg-white/5 hover:text-white'
            ]"
          >
            <span class="material-symbols-outlined text-[18px]">dashboard</span>
            <span>Dashboard Studio</span>
          </Link>

          <!-- Finance Group -->
          <div class="pt-3 px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
            Manajemen Keuangan
          </div>
          <Link
            :href="route('finance.invoices')"
            :class="[
              'flex items-center justify-between px-3 py-2.5 rounded-xl font-semibold transition-all',
              route().current('finance.invoices')
                ? 'bg-[#0062ff] text-white shadow-md'
                : 'text-slate-300 hover:bg-white/5 hover:text-white'
            ]"
          >
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-[18px]">receipt_long</span>
              <span>Invoice & Tagihan</span>
            </div>
          </Link>

          <Link
            :href="route('finance.expenses')"
            :class="[
              'flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all',
              route().current('finance.expenses')
                ? 'bg-[#0062ff] text-white shadow-md'
                : 'text-slate-300 hover:bg-white/5 hover:text-white'
            ]"
          >
            <span class="material-symbols-outlined text-[18px]">payments</span>
            <span>Pengeluaran (Biaya)</span>
          </Link>

          <Link
            :href="route('finance.cashflow')"
            :class="[
              'flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all',
              route().current('finance.cashflow')
                ? 'bg-[#0062ff] text-white shadow-md'
                : 'text-slate-300 hover:bg-white/5 hover:text-white'
            ]"
          >
            <span class="material-symbols-outlined text-[18px]">query_stats</span>
            <span>Arus Kas & Laba Rugi</span>
          </Link>

          <!-- Operations Group -->
          <div class="pt-3 px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
            Operasional
          </div>
          <Link
            :href="route('projects.index')"
            :class="[
              'flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all',
              route().current('projects.*')
                ? 'bg-[#0062ff] text-white shadow-md'
                : 'text-slate-300 hover:bg-white/5 hover:text-white'
            ]"
          >
            <span class="material-symbols-outlined text-[18px]">task_alt</span>
            <span>Proyek IT</span>
          </Link>

          <Link
            :href="route('clients.index')"
            :class="[
              'flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all',
              route().current('clients.*')
                ? 'bg-[#0062ff] text-white shadow-md'
                : 'text-slate-300 hover:bg-white/5 hover:text-white'
            ]"
          >
            <span class="material-symbols-outlined text-[18px]">group</span>
            <span>Klien CRM</span>
          </Link>

          <Link
            :href="route('portfolio.index')"
            :class="[
              'flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all',
              route().current('portfolio.*')
                ? 'bg-[#0062ff] text-white shadow-md'
                : 'text-slate-300 hover:bg-white/5 hover:text-white'
            ]"
          >
            <span class="material-symbols-outlined text-[18px]">photo_library</span>
            <span>Portofolio Web</span>
          </Link>
        </nav>
      </div>

      <!-- Sidebar Footer / Web Shortcut -->
      <div class="p-4 border-t border-white/10 space-y-2">
        <a
          href="/portofolio.html"
          target="_blank"
          class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-colors"
        >
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px]">public</span>
            <span>Lihat Website Publik</span>
          </div>
          <span class="material-symbols-outlined text-[14px]">open_in_new</span>
        </a>
      </div>
    </aside>

    <!-- Main App Container -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Topbar Header -->
      <header class="h-16 bg-white border-b border-[#d2dced] flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20">
        <!-- Title or Breadcrumb -->
        <div class="flex items-center gap-3">
          <slot name="header-title">
            <h1 class="font-extrabold text-base sm:text-lg text-[#08143a]">
              {{ pageTitle }}
            </h1>
          </slot>
        </div>

        <!-- User & Actions -->
        <div class="flex items-center gap-3">
          <div class="hidden sm:flex flex-col text-right">
            <span class="text-xs font-bold text-[#08143a]">{{ user?.name || 'Admin CiptaCoding' }}</span>
            <span class="text-[10px] text-[#0062ff] font-semibold uppercase">{{ user?.role || 'Owner' }}</span>
          </div>

          <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors"
            title="Keluar"
          >
            <span class="material-symbols-outlined text-[20px]">logout</span>
          </Link>
        </div>
      </header>

      <!-- Flash Notification Banner -->
      <div
        v-if="$page.props.flash?.success"
        class="bg-emerald-500 text-white text-xs sm:text-sm font-semibold py-2.5 px-4 sm:px-6 flex items-center justify-between shadow-xs"
      >
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-[18px]">check_circle</span>
          <span>{{ $page.props.flash.success }}</span>
        </div>
      </div>

      <!-- Main Content View -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  }
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const pageTitle = computed(() => props.title);
</script>

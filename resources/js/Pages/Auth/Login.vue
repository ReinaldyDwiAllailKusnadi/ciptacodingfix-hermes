<template>
  <div class="min-h-screen bg-[#08143a] flex items-center justify-center p-4 font-body relative overflow-hidden">
    <!-- Glow decorative background -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#0062ff]/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#0062ff]/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-100 relative z-10">
      <div class="text-center space-y-3 mb-8">
        <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-[#edf4ff] border border-[#d2dced] shadow-xs">
          <img src="/assets/images/logo.png" alt="Logo CiptaCoding" class="h-10 w-auto" />
        </div>
        <h1 class="text-2xl font-extrabold tracking-tight text-[#08143a]">
          Cipta<span class="text-[#0062ff]">Coding</span> Studio
        </h1>
        <p class="text-xs text-[#495874]">
          Portal Manajemen Operasional, Keuangan & Portofolio
        </p>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-[#08143a] mb-1.5">Email</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">mail</span>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-[#d2dced] rounded-xl text-sm focus:ring-2 focus:ring-[#0062ff] focus:border-transparent outline-none transition-all"
              placeholder="admin@ciptacoding.com"
            />
          </div>
          <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">
            {{ form.errors.email }}
          </p>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#08143a] mb-1.5">Password</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">lock</span>
            <input
              v-model="form.password"
              type="password"
              required
              class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-[#d2dced] rounded-xl text-sm focus:ring-2 focus:ring-[#0062ff] focus:border-transparent outline-none transition-all"
              placeholder="Password"
            />
          </div>
          <p v-if="form.errors.password" class="text-xs text-red-500 mt-1">
            {{ form.errors.password }}
          </p>
        </div>

        <div class="p-3 bg-[#edf4ff] rounded-xl border border-[#d2dced]/70 text-[11px] text-[#495874] space-y-1">
          <div class="font-bold text-[#0062ff] flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">info</span> Akun Default Administrator:
          </div>
          <div class="font-mono text-slate-700">Email: admin@ciptacoding.com</div>
          <div class="font-mono text-slate-700">Password: ciptacoding2026</div>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 bg-[#0062ff] hover:bg-[#0051d4] text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 mt-4 disabled:opacity-70"
        >
          <span>{{ form.processing ? 'Memproses...' : 'Masuk ke Dashboard' }}</span>
          <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  email: 'admin@ciptacoding.com',
  password: 'ciptacoding2026',
  remember: true,
});

function submit() {
  form.post(route('login.post'));
}
</script>

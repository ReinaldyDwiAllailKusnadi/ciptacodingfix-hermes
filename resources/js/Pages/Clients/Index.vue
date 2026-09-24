<template>
  <AdminLayout title="Buku Kontak & CRM Klien">
    <div class="space-y-6">

      <!-- Action Bar -->
      <div class="bg-white p-4 rounded-2xl border border-[#d2dced] shadow-xs flex items-center justify-between gap-3">
        <div>
          <h2 class="font-extrabold text-sm sm:text-base text-[#08143a]">Daftar Klien Studio</h2>
          <p class="text-xs text-[#495874]">Data kontak, bisnis, dan histori proyek klien CiptaCoding</p>
        </div>

        <button
          @click="openModal(null)"
          class="inline-flex items-center gap-2 bg-[#0062ff] hover:bg-[#0051d4] text-white px-4 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all"
        >
          <span class="material-symbols-outlined text-[18px]">person_add</span>
          <span>Tambah Klien Baru</span>
        </button>
      </div>

      <!-- Clients Table -->
      <div class="bg-white rounded-2xl border border-[#d2dced] shadow-xs overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-slate-50/80 border-b border-[#d2dced] text-[#495874] font-bold text-xs uppercase tracking-wider">
              <tr>
                <th class="py-3.5 px-4">Nama Klien</th>
                <th class="py-3.5 px-4">Perusahaan / Usaha</th>
                <th class="py-3.5 px-4">Kontak WhatsApp</th>
                <th class="py-3.5 px-4">Status</th>
                <th class="py-3.5 px-4">Histori Proyek</th>
                <th class="py-3.5 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="c in clients" :key="c.id" class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4">
                  <div class="font-bold text-[#08143a]">{{ c.name }}</div>
                  <div class="text-[11px] text-slate-400">{{ c.email || '-' }}</div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-[#08143a]">{{ c.company || 'Personal' }}</div>
                  <div class="text-[11px] text-[#495874] line-clamp-1" v-if="c.address">{{ c.address }}</div>
                </td>
                <td class="py-3.5 px-4">
                  <a
                    v-if="c.whatsapp"
                    :href="`https://wa.me/${c.whatsapp.replace(/^0/, '62')}`"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs transition-colors"
                  >
                    <span class="material-symbols-outlined text-[16px]">chat</span>
                    <span>{{ c.whatsapp }}</span>
                  </a>
                  <span v-else class="text-slate-400">-</span>
                </td>
                <td class="py-3.5 px-4">
                  <span
                    :class="[
                      'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                      c.status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'
                    ]"
                  >
                    {{ c.status === 'active' ? 'Aktif' : 'Prospek' }}
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-1 font-semibold text-xs text-[#0062ff]">
                    <span class="material-symbols-outlined text-[16px]">folder</span>
                    <span>{{ c.projects_count }} Proyek</span>
                  </div>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button @click="openModal(c)" class="p-1.5 text-[#0062ff] hover:bg-[#edf4ff] rounded-lg transition-colors">
                      <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                    <button @click="deleteClient(c)" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
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

    <!-- MODAL: TAMBAH / EDIT KLIEN -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-[#d2dced] pb-3">
          <h3 class="font-extrabold text-base text-[#08143a]">
            {{ isEditing ? 'Edit Data Klien' : 'Tambah Klien Baru' }}
          </h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-3.5 text-xs">
          <div>
            <label class="block font-bold text-[#08143a] mb-1">Nama Lengkap Klien *</label>
            <input v-model="form.name" type="text" required class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Contoh: Budi Pratama"/>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Nama Perusahaan / Bisnis</label>
              <input v-model="form.company" type="text" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Contoh: Kopi Serasi & Bakery"/>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Status Klien</label>
              <select v-model="form.status" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none">
                <option value="active">Klien Aktif</option>
                <option value="prospect">Prospek / Lead</option>
                <option value="inactive">Non-Aktif</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Nomor WhatsApp</label>
              <input v-model="form.whatsapp" type="text" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="081234567890"/>
            </div>
            <div>
              <label class="block font-bold text-[#08143a] mb-1">Alamat Email</label>
              <input v-model="form.email" type="email" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="klien@perusahaan.com"/>
            </div>
          </div>

          <div>
            <label class="block font-bold text-[#08143a] mb-1">Alamat Domisili / Kota</label>
            <input v-model="form.address" type="text" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Bandung, Jawa Barat"/>
          </div>

          <div>
            <label class="block font-bold text-[#08143a] mb-1">Catatan Tambahan</label>
            <textarea v-model="form.notes" rows="2" class="w-full px-3.5 py-2 bg-slate-50 border border-[#d2dced] rounded-xl outline-none" placeholder="Kebutuhan khusus atau catatan diskusi..."></textarea>
          </div>

          <div class="pt-3 border-t border-[#d2dced] flex justify-end gap-2">
            <button type="button" @click="showModal = false" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-xl">Batal</button>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-[#0062ff] hover:bg-[#0051d4] text-white font-bold rounded-xl shadow-md">
              {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Perbarui Klien' : 'Simpan Klien') }}
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
  clients: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
  name: '',
  company: '',
  whatsapp: '',
  email: '',
  address: '',
  status: 'active',
  notes: '',
});

function openModal(client = null) {
  if (client) {
    isEditing.value = true;
    editingId.value = client.id;
    form.name = client.name;
    form.company = client.company || '';
    form.whatsapp = client.whatsapp || '';
    form.email = client.email || '';
    form.address = client.address || '';
    form.status = client.status;
    form.notes = client.notes || '';
  } else {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
  }
  showModal.value = true;
}

function submitForm() {
  if (isEditing.value) {
    form.put(route('clients.update', editingId.value), {
      onSuccess: () => (showModal.value = false)
    });
  } else {
    form.post(route('clients.store'), {
      onSuccess: () => (showModal.value = false)
    });
  }
}

function deleteClient(c) {
  if (confirm(`Hapus data klien "${c.name}"? Seluruh proyek dan invoice klien ini akan ikut terhapus.`)) {
    router.delete(route('clients.destroy', c.id));
  }
}
</script>

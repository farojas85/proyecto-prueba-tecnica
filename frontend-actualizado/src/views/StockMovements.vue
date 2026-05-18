<template>
  <div class="space-y-6">
    <div class="flex items-center space-x-2 text-sm text-slate-400">
      <router-link to="/products" class="hover:text-brand-600 transition-colors">Productos</router-link>
      <span>/</span>
      <span class="text-slate-600 font-semibold">Movimientos de Stock</span>
    </div>

    <div>
      <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Historial de Movimientos</h1>
      <p class="text-sm text-slate-500 mt-1">Registra entradas o salidas físicas y monitorea auditorías en tiempo real</p>
    </div>

    <p v-if="error" class="bg-rose-500/10 border border-rose-500/20 text-rose-600 px-4 py-3 rounded-xl text-sm font-medium">
      {{ error }}
    </p>
    <p v-if="success" class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 px-4 py-3 rounded-xl text-sm font-medium">
      {{ success }}
    </p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Register Stock Adjustment Card -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 lg:col-span-1">
        <h3 class="text-lg font-bold text-slate-900">Registrar Ajuste</h3>
        
        <div class="space-y-3">
          <!-- Tipo de Movimiento -->
          <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tipo de Movimiento</label>
            <select 
              v-model="form.type" 
              :disabled="isSaving"
              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm font-semibold transition-all"
            >
              <option value="entrada">Entrada</option>
              <option value="salida">Salida</option>
            </select>
          </div>

          <!-- Cantidad -->
          <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Cantidad</label>
            <input 
              type="number" 
              v-model="form.quantity" 
              placeholder="Ej. 15" 
              :disabled="isSaving"
              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm transition-all"
            />
          </div>

          <!-- Motivo -->
          <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Motivo / Concepto</label>
            <input 
              v-model="form.reason" 
              placeholder="Ej. Compra de proveedor" 
              :disabled="isSaving"
              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm transition-all"
            />
          </div>
        </div>

        <button 
          @click="save" 
          :disabled="isSaving"
          class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition-all shadow-sm"
        >
          {{ isSaving ? 'Registrando...' : 'Registrar Movimiento' }}
        </button>
      </div>

      <!-- Movements Log Table Card -->
      <div class="lg:col-span-2 space-y-4">
        <!-- Page size selector -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
          <span class="text-sm font-semibold text-slate-500">Listado de transacciones</span>
          <div class="flex items-center space-x-2 text-sm text-slate-500">
            <span>Mostrar:</span>
            <select 
              v-model="perPage" 
              @change="load(1)"
              class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm font-semibold transition-all"
            >
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div v-if="isLoading" class="p-12 text-center text-slate-500 flex flex-col items-center justify-center space-y-3">
            <svg class="animate-spin h-8 w-8 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-semibold text-slate-600">Cargando transacciones...</span>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                  <th class="py-4 px-6">ID</th>
                  <th class="py-4 px-6">Tipo</th>
                  <th class="py-4 px-6">Cantidad</th>
                  <th class="py-4 px-6">Concepto / Motivo</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                <tr v-for="m in movements" :key="m.id" class="hover:bg-slate-50/50 transition-colors">
                  <td class="py-4 px-6 font-semibold text-slate-400">#{{ m.id }}</td>
                  <td class="py-4 px-6">
                    <span 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider"
                      :class="m.type === 'entrada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100'"
                    >
                      {{ m.type }}
                    </span>
                  </td>
                  <td class="py-4 px-6 font-bold" :class="m.type === 'entrada' ? 'text-emerald-600' : 'text-rose-600'">
                    {{ m.type === 'entrada' ? '+' : '-' }}{{ m.quantity }} uds
                  </td>
                  <td class="py-4 px-6 font-semibold text-slate-700">{{ m.reason }}</td>
                </tr>
                <tr v-if="movements.length === 0">
                  <td colspan="4" class="py-8 px-6 text-center text-slate-400">Sin movimientos registrados aún para este producto.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
          <button 
            :disabled="pagination.current_page === 1" 
            @click="load(pagination.current_page - 1)"
            class="px-4 py-2 border border-slate-200 text-sm font-semibold rounded-xl text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:hover:bg-white transition-all"
          >
            Anterior
          </button>
          <span class="text-sm font-semibold text-slate-500">
            Página {{ pagination.current_page }} de {{ pagination.last_page }}
          </span>
          <button 
            :disabled="pagination.current_page === pagination.last_page" 
            @click="load(pagination.current_page + 1)"
            class="px-4 py-2 border border-slate-200 text-sm font-semibold rounded-xl text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:hover:bg-white transition-all"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api'

const route = useRoute()

const movements = ref([])
const form = reactive({ type: 'entrada', quantity: '', reason: '' })
const error = ref('')
const success = ref('')
const isLoading = ref(false)
const isSaving = ref(false)

const perPage = ref(10)
const pagination = reactive({ current_page: 1, last_page: 1 })

const load = async (page = 1) => {
  isLoading.value = true
  try {
    const res = await api.get('/products/' + route.params.id + '/stock-movements', {
      params: { page, per_page: perPage.value }
    })
    
    const data = res.data
    if (data.data) {
      movements.value = data.data
      pagination.current_page = data.current_page || 1
      pagination.last_page = data.last_page || 1
    } else {
      movements.value = data
    }
  } catch (err) {
    error.value = 'Error cargando movimientos'
  } finally {
    isLoading.value = false
  }
}

const save = async () => {
  if (!form.quantity || !form.reason) {
    error.value = 'Completa todos los campos'
    return
  }
  error.value = ''
  success.value = ''
  isSaving.value = true

  try {
    await api.post('/products/' + route.params.id + '/stock-movements', form)
    success.value = 'Movimiento registrado con éxito'
    form.quantity = ''
    form.reason = ''
    await load(1)
  } catch (err) {
    error.value = err.response?.data?.message || 'Error registrando movimiento'
  } finally {
    isSaving.value = false
  }
}

onMounted(() => load())
</script>

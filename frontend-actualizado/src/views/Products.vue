<template>
  <div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Catálogo de Productos</h1>
        <p class="text-sm text-slate-500 mt-1">Crea, edita y registra movimientos de inventario de tus productos</p>
      </div>
      <div>
        <router-link to="/products/new" class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-xl text-white bg-brand-600 hover:bg-brand-700 shadow-sm shadow-brand-200 hover:shadow-md transition-all">
          Nuevo Producto
        </router-link>
      </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex-1 flex gap-2">
        <input 
          v-model="search" 
          placeholder="Buscar producto por nombre..." 
          @keyup.enter="load(1)" 
          class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all"
        />
        <button 
          @click="load(1)" 
          :disabled="isLoading"
          class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-all"
        >
          Buscar
        </button>
      </div>
      <div class="flex items-center space-x-2 text-sm text-slate-500">
        <span>Mostrar:</span>
        <select 
          v-model="perPage" 
          @change="load(1)"
          class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm font-semibold transition-all"
        >
          <option :value="10">10 por página</option>
          <option :value="25">25 por página</option>
          <option :value="50">50 por página</option>
          <option :value="75">75 por página</option>
          <option :value="100">100 por página</option>
        </select>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div v-if="isLoading" class="p-12 text-center text-slate-500 flex flex-col items-center justify-center space-y-3">
        <svg class="animate-spin h-8 w-8 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm font-semibold text-slate-600">Cargando catálogo...</span>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
              <th class="py-4 px-6">ID</th>
              <th class="py-4 px-6">Nombre</th>
              <th class="py-4 px-6">Precio</th>
              <th class="py-4 px-6">Stock</th>
              <th class="py-4 px-6 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
            <tr v-for="p in products" :key="p.id" class="hover:bg-slate-50/50 transition-colors">
              <td class="py-4 px-6 font-semibold text-slate-400">#{{ p.id }}</td>
              <td class="py-4 px-6 font-bold text-slate-900">{{ p.name }}</td>
              <td class="py-4 px-6 font-semibold text-slate-600">${{ p.price.toFixed(2) }}</td>
              <td class="py-4 px-6">
                <span 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-rose-50 text-rose-700 border border-rose-100': p.stock < 10,
                    'bg-amber-50 text-amber-700 border border-amber-100': p.stock >= 10 && p.stock < 25,
                    'bg-emerald-50 text-emerald-700 border border-emerald-100': p.stock >= 25
                  }"
                >
                  {{ p.stock }} uds
                </span>
              </td>
              <td class="py-4 px-6 text-right space-x-1">
                <button @click="edit(p.id)" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs transition-all">
                  Editar
                </button>
                <button @click="stock(p.id)" class="px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 font-semibold rounded-lg text-xs transition-all">
                  Movimientos
                </button>
                <button @click="remove(p.id)" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded-lg text-xs transition-all">
                  Eliminar
                </button>
              </td>
            </tr>
            <tr v-if="products.length === 0">
              <td colspan="5" class="py-8 px-6 text-center text-slate-400">No se encontraron productos en el catálogo.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination Controls -->
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
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()

const products = ref([])
const search = ref('')
const perPage = ref(10)
const isLoading = ref(false)
const pagination = reactive({ current_page: 1, last_page: 1 })

const load = async (page = 1) => {
  isLoading.value = true
  try {
    const res = await api.get('/products', { 
      params: { search: search.value, page, per_page: perPage.value } 
    })
    const data = res.data
    if (data.data) {
      products.value = data.data
      pagination.current_page = data.current_page || 1
      pagination.last_page = data.last_page || 1
    } else {
      products.value = data
    }
  } catch (err) {
    console.error(err)
  } finally {
    isLoading.value = false
  }
}

const edit = (id) => router.push('/products/' + id + '/edit')
const stock = (id) => router.push('/products/' + id + '/stock')

const remove = async (id) => {
  if (confirm('¿Eliminar producto?')) {
    try {
      await api.delete('/products/' + id)
      load(pagination.current_page)
    } catch (err) {
      alert('Error eliminando')
    }
  }
}

onMounted(() => load())
</script>

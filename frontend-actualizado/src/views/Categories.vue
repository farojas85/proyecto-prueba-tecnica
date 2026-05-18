<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Categorías</h1>
      <p class="text-sm text-slate-500 mt-1">Administra las clasificaciones de tus productos de forma rápida</p>
    </div>

    <!-- Feedback Toasts -->
    <div v-if="error" class="bg-rose-500/10 border border-rose-500/20 text-rose-600 px-4 py-3 rounded-xl text-sm font-medium">
      {{ error }}
    </div>
    <div v-if="success" class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 px-4 py-3 rounded-xl text-sm font-medium">
      {{ success }}
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Create/Edit Form Card -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 lg:col-span-1">
        <h3 class="text-lg font-bold text-slate-900">
          {{ form.id ? 'Editar Categoría' : 'Nueva Categoría' }}
        </h3>
        
        <div class="space-y-3">
          <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Nombre</label>
            <input 
              v-model="form.name" 
              placeholder="Ej. Electrónica" 
              :disabled="isSaving"
              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm transition-all"
            />
          </div>

          <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Descripción</label>
            <input 
              v-model="form.description" 
              placeholder="Breve descripción..." 
              :disabled="isSaving"
              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm transition-all"
            />
          </div>
        </div>

        <div class="flex items-center space-x-2 pt-2">
          <button 
            @click="save" 
            :disabled="isSaving"
            class="flex-1 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm"
          >
            {{ isSaving ? 'Guardando...' : 'Guardar' }}
          </button>
          <button 
            v-if="form.id" 
            @click="cancelEdit" 
            :disabled="isSaving"
            class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition-all"
          >
            Cancelar
          </button>
        </div>
      </div>

      <!-- Categories List Card -->
      <div class="lg:col-span-2 space-y-4">
        <!-- Search & Filter Category Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div class="flex-1 flex gap-2">
            <input 
              v-model="search" 
              placeholder="Buscar categoría..." 
              @keyup.enter="load(1)" 
              class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm transition-all"
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
            <span class="text-sm font-semibold text-slate-600">Cargando categorías...</span>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                  <th class="py-4 px-6">ID</th>
                  <th class="py-4 px-6">Nombre</th>
                  <th class="py-4 px-6">Estado</th>
                  <th class="py-4 px-6 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                <tr v-for="c in categories" :key="c.id" class="hover:bg-slate-50/50 transition-colors">
                  <td class="py-4 px-6 font-semibold text-slate-400">#{{ c.id }}</td>
                  <td class="py-4 px-6 font-bold text-slate-900">
                    <div>
                      <p>{{ c.name }}</p>
                      <p class="text-xs text-slate-400 font-normal mt-0.5">{{ c.description }}</p>
                    </div>
                  </td>
                  <td class="py-4 px-6">
                    <span 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                      :class="c.status ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-700 border border-slate-200'"
                    >
                      {{ c.status ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="py-4 px-6 text-right space-x-1">
                    <button @click="edit(c)" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs transition-all">
                      Editar
                    </button>
                    <button @click="remove(c.id)" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded-lg text-xs transition-all">
                      Eliminar
                    </button>
                  </td>
                </tr>
                <tr v-if="categories.length === 0">
                  <td colspan="4" class="py-8 px-6 text-center text-slate-400">No se encontraron categorías.</td>
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
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '../api'

const categories = ref([])
const form = reactive({ id: null, name: '', description: '', status: 1 })
const error = ref('')
const success = ref('')
const isLoading = ref(false)
const isSaving = ref(false)

const search = ref('')
const perPage = ref(10)
const pagination = reactive({ current_page: 1, last_page: 1 })

const load = async (page = 1) => {
  isLoading.value = true
  try {
    const res = await api.get('/categories', {
      params: { search: search.value, page, per_page: perPage.value }
    })
    
    const data = res.data
    if (data.data) {
      categories.value = data.data
      pagination.current_page = data.current_page || 1
      pagination.last_page = data.last_page || 1
    } else {
      categories.value = data.categories || data
    }
  } catch (err) {
    error.value = 'Error al cargar categorías'
  } finally {
    isLoading.value = false
  }
}

const edit = (c) => {
  form.id = c.id
  form.name = c.name
  form.description = c.description
  form.status = c.status
}

const cancelEdit = () => {
  form.id = null
  form.name = ''
  form.description = ''
  form.status = 1
}

const save = async () => {
  if (!form.name) {
    error.value = 'Nombre obligatorio'
    return
  }
  error.value = ''
  success.value = ''
  isSaving.value = true

  const url = '/categories' + (form.id ? '/' + form.id : '')
  const method = form.id ? 'put' : 'post'

  try {
    await api({ method, url, data: form })
    success.value = 'Guardado exitosamente'
    cancelEdit()
    await load(pagination.current_page)
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al guardar'
  } finally {
    isSaving.value = false
  }
}

const remove = async (id) => {
  if (confirm('¿Eliminar categoría?')) {
    try {
      await api.delete('/categories/' + id)
      success.value = 'Eliminada correctamente'
      await load(pagination.current_page)
    } catch (err) {
      error.value = err.response?.data?.error || 'Error eliminando categoría'
    }
  }
}

onMounted(() => load())
</script>

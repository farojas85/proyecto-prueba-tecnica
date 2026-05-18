<template>
  <div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-2 text-sm text-slate-400">
      <router-link to="/products" class="hover:text-brand-600 transition-colors">Productos</router-link>
      <span>/</span>
      <span class="text-slate-600 font-semibold">{{ isEdit ? 'Editar' : 'Nuevo' }}</span>
    </div>

    <div>
      <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
        {{ isEdit ? 'Editar Producto' : 'Crear Nuevo Producto' }}
      </h1>
      <p class="text-sm text-slate-500 mt-1">Completa los campos del formulario para guardar el artículo</p>
    </div>

    <p v-if="error" class="bg-rose-500/10 border border-rose-500/20 text-rose-600 px-4 py-3 rounded-xl text-sm font-medium">
      {{ error }}
    </p>

    <div v-if="isLoading" class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm flex justify-center py-12">
      <svg class="animate-spin h-8 w-8 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>

    <div v-else class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
      <!-- Nombre -->
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Nombre del Producto</label>
        <input 
          v-model="form.name" 
          placeholder="Ej. iPhone 15 Pro"
          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all"
        />
      </div>

      <!-- Descripción -->
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Descripción detallada</label>
        <textarea 
          v-model="form.description" 
          placeholder="Escribe una breve descripción del artículo..."
          rows="4"
          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all resize-none"
        ></textarea>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Precio -->
        <div class="space-y-2">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Precio (USD)</label>
          <input 
            type="number" 
            step="0.01"
            v-model="form.price" 
            placeholder="0.00"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all"
          />
        </div>

        <!-- Stock -->
        <div class="space-y-2">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Stock Inicial</label>
          <input 
            type="number" 
            v-model="form.stock" 
            :disabled="isEdit"
            placeholder="0"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all disabled:opacity-50 disabled:bg-slate-100"
          />
          <p v-if="isEdit" class="text-[10px] text-slate-400 font-semibold">El stock solo se altera mediante movimientos de stock.</p>
        </div>
      </div>

      <!-- Categoría -->
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Categoría Asociada</label>
        <select 
          v-model="form.category_id"
          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm font-semibold transition-all"
        >
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>

      <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
        <router-link to="/products" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-sm transition-all">
          Cancelar
        </router-link>
        <button 
          @click="save" 
          :disabled="isSaving"
          class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-sm transition-all shadow-sm shadow-brand-200"
        >
          {{ isSaving ? 'Guardando...' : 'Guardar Producto' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../api'

const router = useRouter()
const route = useRoute()

const form = reactive({
  name: '',
  description: '',
  price: 0,
  stock: 0,
  category_id: 1,
  status: 1
})

const error = ref('')
const isLoading = ref(false)
const isSaving = ref(false)
const categories = ref([])

const isEdit = computed(() => !!route.params.id)

const loadCategories = async () => {
  try {
    const res = await api.get('/categories', { params: { per_page: 100 } })
    categories.value = res.data.data || []
  } catch (err) {
    console.error(err)
  }
}

const load = async () => {
  if (isEdit.value) {
    isLoading.value = true
    try {
      const res = await api.get('/products/' + route.params.id)
      const data = res.data.data
      form.name = data.name
      form.description = data.description
      form.price = data.price
      form.stock = data.stock
      form.category_id = data.category_id
      form.status = data.status
    } catch (err) {
      error.value = 'Error cargando producto'
    } finally {
      isLoading.value = false
    }
  }
}

const save = async () => {
  error.value = ''
  isSaving.value = true
  try {
    if (isEdit.value) {
      await api.put('/products/' + route.params.id, form)
    } else {
      await api.post('/products', form)
    }
    router.push('/products')
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al guardar'
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  loadCategories()
  load()
})
</script>

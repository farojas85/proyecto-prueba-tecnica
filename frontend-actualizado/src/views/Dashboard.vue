<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard</h1>
        <p class="text-sm text-slate-500 mt-1">Monitorea el inventario y movimientos en tiempo real</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-pulse">
      <div v-for="i in 4" :key="i" class="h-32 bg-slate-200 rounded-2xl"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-rose-500/10 border border-rose-500/20 text-rose-600 px-6 py-4 rounded-2xl text-sm font-medium">
      {{ error }}
    </div>

    <!-- Stat Cards Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <!-- Total Products -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-32 relative overflow-hidden group">
        <div class="absolute right-4 top-4 text-brand-500/10 group-hover:scale-110 transition-transform duration-300">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
        </div>
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Productos</span>
        <span class="text-3xl font-black text-slate-900">{{ stats.products_count }}</span>
      </div>

      <!-- Low Stock Warning -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-32 relative overflow-hidden group">
        <div class="absolute right-4 top-4 text-rose-500/10 group-hover:scale-110 transition-transform duration-300">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Stock Bajo</span>
        <span class="text-3xl font-black" :class="stats.low_stock_count > 0 ? 'text-rose-600' : 'text-slate-900'">
          {{ stats.low_stock_count }}
        </span>
      </div>

      <!-- Inputs Month -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-32 relative overflow-hidden group">
        <div class="absolute right-4 top-4 text-emerald-500/10 group-hover:scale-110 transition-transform duration-300">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path>
          </svg>
        </div>
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Entradas (Mes)</span>
        <span class="text-3xl font-black text-emerald-600">{{ stats.movements_in_month }}</span>
      </div>

      <!-- Outputs Month -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-32 relative overflow-hidden group">
        <div class="absolute right-4 top-4 text-amber-500/10 group-hover:scale-110 transition-transform duration-300">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0-3a9 9 0 110 18 9 9 0 010-18z"></path>
          </svg>
        </div>
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Salidas (Mes)</span>
        <span class="text-3xl font-black text-amber-600">{{ stats.movements_out_month }}</span>
      </div>
    </div>

    <!-- Quick Navigation Shortcuts -->
    <div class="pt-6 border-t border-slate-200">
      <h3 class="text-lg font-bold text-slate-900 mb-4">Accesos Rápidos</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <router-link to="/products" class="flex items-center justify-between p-4 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl group transition-all">
          <div class="flex items-center space-x-3">
            <span class="p-2 bg-brand-50 text-brand-600 rounded-lg group-hover:bg-brand-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </span>
            <span class="text-sm font-semibold text-slate-700">Administrar Catálogo de Productos</span>
          </div>
          <svg class="w-5 h-5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </router-link>

        <router-link to="/categories" class="flex items-center justify-between p-4 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl group transition-all">
          <div class="flex items-center space-x-3">
            <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </span>
            <span class="text-sm font-semibold text-slate-700">Administrar Categorías</span>
          </div>
          <svg class="w-5 h-5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../api'

const stats = ref({})
const isLoading = ref(true)
const error = ref('')

const loadData = async () => {
  isLoading.value = true
  try {
    const res = await api.get('/dashboard')
    stats.value = res.data
  } catch (err) {
    error.value = 'Error al cargar métricas'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>

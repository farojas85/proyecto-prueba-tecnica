<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Navbar Premium -->
    <nav v-if="authStore.token" class="bg-white/80 border-b border-slate-200/80 sticky top-0 z-50 backdrop-blur-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center space-x-8">
            <div class="flex-shrink-0 flex items-center">
              <span class="text-xl font-black bg-gradient-to-r from-brand-600 to-indigo-600 bg-clip-text text-transparent tracking-tight">
                LEGACY CORE
              </span>
            </div>
            <div class="hidden sm:flex sm:space-x-8 h-full">
              <router-link to="/dashboard" class="border-transparent text-slate-500 hover:border-brand-500 hover:text-slate-700 inline-flex items-center px-1 h-full border-b-2 text-sm font-medium transition-all" active-class="!border-brand-600 !text-brand-600 font-semibold">
                Dashboard
              </router-link>
              <router-link to="/products" class="border-transparent text-slate-500 hover:border-brand-500 hover:text-slate-700 inline-flex items-center px-1 h-full border-b-2 text-sm font-medium transition-all" active-class="!border-brand-600 !text-brand-600 font-semibold">
                Productos
              </router-link>
              <router-link to="/categories" class="border-transparent text-slate-500 hover:border-brand-500 hover:text-slate-700 inline-flex items-center px-1 h-full border-b-2 text-sm font-medium transition-all" active-class="!border-brand-600 !text-brand-600 font-semibold">
                Categorías
              </router-link>
            </div>
          </div>
          <div class="flex items-center">
            <button @click="logout" class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-semibold uppercase tracking-wider rounded-full shadow-sm text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-all">
              Salir
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const logout = () => {
  authStore.logout()
  router.push('/login')
}
</script>

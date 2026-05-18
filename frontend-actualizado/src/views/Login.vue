<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-900 px-4 py-12 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-slate-800 p-8 rounded-2xl shadow-xl border border-slate-700/50 relative overflow-hidden">
      <!-- Decorative Background Gradient Grid -->
      <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-600/30 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

      <div class="relative z-10">
        <h2 class="text-center text-3xl font-extrabold tracking-tight bg-gradient-to-r from-brand-400 to-indigo-400 bg-clip-text text-transparent">
          Legacy Modernizado
        </h2>
        <p class="mt-2 text-center text-sm text-slate-400">
          Inicia sesión para gestionar el inventario
        </p>
      </div>

      <div class="mt-8 space-y-6 relative z-10">
        <div v-if="error" class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-lg text-sm">
          {{ error }}
        </div>

        <div class="rounded-md space-y-4">
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Correo Electrónico</label>
            <input 
              v-model="email" 
              type="email" 
              required 
              :disabled="isLoading"
              class="appearance-none relative block w-full px-4 py-3 border border-slate-700 bg-slate-900/50 text-slate-100 rounded-xl placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:z-10 text-sm transition-all"
              placeholder="admin@legacy.test" 
            />
          </div>
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Contraseña</label>
            <input 
              v-model="password" 
              type="password" 
              required 
              :disabled="isLoading"
              @keyup.enter="login" 
              class="appearance-none relative block w-full px-4 py-3 border border-slate-700 bg-slate-900/50 text-slate-100 rounded-xl placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:z-10 text-sm transition-all"
              placeholder="••••••••" 
            />
          </div>
        </div>

        <div>
          <button 
            @click="login" 
            :disabled="isLoading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform hover:scale-[1.02] shadow-lg shadow-brand-900/30"
          >
            <span v-if="isLoading" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Ingresando...
            </span>
            <span v-else>Ingresar</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../api'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('admin@legacy.test')
const password = ref('password')
const error = ref('')
const isLoading = ref(false)

const login = async () => {
  error.value = ''
  isLoading.value = true
  try {
    const res = await api.post('/login', {
      email: email.value,
      password: password.value
    })
    authStore.setToken(res.data.token)
    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || err.response?.data?.error || 'Error de red'
  } finally {
    isLoading.value = false
  }
}
</script>

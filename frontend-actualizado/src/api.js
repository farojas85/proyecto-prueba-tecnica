import axios from 'axios'
import router from './router'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api'
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token') // Keep checking localStorage directly to avoid early Pinia injection issues before mount
  if (token) config.headers.Authorization = 'Bearer ' + token
  return config
})

api.interceptors.response.use(
  response => response,
  error => {
    if (error.response && (error.response.status === 401 || error.response.status === 403)) {
      // Lazy load the store
      import('./stores/auth').then(({ useAuthStore }) => {
        const authStore = useAuthStore()
        authStore.logout()
        router.push('/login').catch(() => {})
      })
    }
    return Promise.reject(error)
  }
)

export default api

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from './stores/auth'

import Login from './views/Login.vue'
import Dashboard from './views/Dashboard.vue'
import Products from './views/Products.vue'
import ProductForm from './views/ProductForm.vue'
import Categories from './views/Categories.vue'
import StockMovements from './views/StockMovements.vue'

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: Login, meta: { public: true } },
  { path: '/dashboard', component: Dashboard },
  { path: '/products', component: Products },
  { path: '/products/new', component: ProductForm },
  { path: '/products/:id/edit', component: ProductForm },
  { path: '/products/:id/stock', component: StockMovements },
  { path: '/categories', component: Categories },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  if (!to.meta.public && !authStore.token) {
    next('/login')
  } else {
    next()
  }
})

export default router

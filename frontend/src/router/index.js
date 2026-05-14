import { createRouter, createWebHistory } from 'vue-router'
import DashboardView from '../views/DashboardView.vue'
import ProductsView from '../views/ProductsView.vue'
import SalesView from '../views/SalesView.vue'
import LoginView from '../views/LoginView.vue' 

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: DashboardView,
    },
    {
      path: '/products',
      name: 'products',
      component: ProductsView,
    },
    {
      path: '/sales',
      name: 'sales',
      component: SalesView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    }
  ],
})

export default router

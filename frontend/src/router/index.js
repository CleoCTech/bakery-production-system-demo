import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: () => import('../views/DashboardView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/products',
      name: 'products',
      component: () => import('../views/ProductsView.vue'),
      meta: { requiresAuth: true, roles: ['admin', 'baker'] }
    },
    {
      path: '/sales',
      name: 'sales',
      component: () => import('../views/SalesView.vue'),
      meta: { requiresAuth: true, roles: ['admin', 'cashier'] }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
  ],
})

// NAVIGATION GUARD: checks auth before every page navigation
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  const user = JSON.parse(localStorage.getItem('auth_user') || 'null')

  // If the page requires auth and there's no token → redirect to login
  if (to.meta.requiresAuth && !token) {
    next({ name: 'login' })
    return
  }

  // If the page requires specific roles and user doesn't have one
  if (to.meta.roles && user && !to.meta.roles.includes(user.role)) {
    next({ name: 'dashboard' })  // Redirect to dashboard instead
    return
  }

  // If logged in and trying to access login page → redirect to dashboard
  if (to.name === 'login' && token) {
    next({ name: 'dashboard' })
    return
  }

  next()
})

export default router
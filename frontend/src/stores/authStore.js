import {defineStore} from 'pinia';
import {ref, computed} from 'vue';
import api from '../api/axios'
import router from '../router'

export const useAuthStore = defineStore('auth', () => {

    // STATE: user info and token
    const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))
    const token = ref(localStorage.getItem('auth_token') || null)
    const isLoading = ref(false)
    const error = ref(null)

    // GETTERS
    const isLoggedIn = computed(() => !!token.value)
    const userRole = computed(() => user.value?.role || null)
    const userName = computed(() => user.value?.name || '')
    const isAdmin = computed(() => userRole.value === 'admin')
    const isBaker = computed(() => userRole.value === 'baker')
    const isCashier = computed(() => userRole.value === 'cashier')

    // ACTIONS
    async function login(email, password) {
        isLoading.value = true
        error.value = null

        try {
            const response = await api.post('/login', { email, password })
            const { user: userData, token: authToken } = response.data

            // Store in state
            user.value = userData
            token.value = authToken

            // Persist to localStorage (survives page refresh)
            localStorage.setItem('auth_user', JSON.stringify(userData))
            localStorage.setItem('auth_token', authToken)

            // Navigate to dashboard
            router.push('/')
        } catch (err) {
            if (err.response?.status === 401) {
                error.value = 'Invalid email or password'
            } else {
                error.value = 'Login failed. Is the server running?'
            }
            throw err
        } finally {
            isLoading.value = false
        }
    }

    async function logout() {
        try {
            await api.post('/logout')
        } catch (err) {
            // Even if the API call fails, clear local state
            console.error('Logout API error:', err)
        }

        // Clear everything
        user.value = null
        token.value = null
        localStorage.removeItem('auth_user')
        localStorage.removeItem('auth_token')

        router.push('/login')
    }

    // Check if stored token is still valid
    async function checkAuth() {
        if (!token.value) return false

        try {
            const response = await api.get('/me')
            user.value = response.data
            return true
        } catch (err) {
        // Token expired or invalid
        user.value = null
        token.value = null
        localStorage.removeItem('auth_user')
        localStorage.removeItem('auth_token')
        return false
        }
    }

    return {
        user, token, isLoading, error,
        isLoggedIn, userRole, userName, isAdmin, isBaker, isCashier,
        login, logout, checkAuth,
    }

})
// ---------------------------------------------------------------
// AXIOS INSTANCE: configured to talk to your Laravel API
//
// This file sets up:
// 1. Base URL — '/api' (Vite proxy forwards to localhost:8000)
// 2. Headers — sends JSON and accepts JSON
// 3. Auth token — automatically attached to every request
// 4. Error handling — catches 401 (expired token) and redirects to login
//
// Every store and component imports THIS instance, not raw axios.
// ---------------------------------------------------------------
import axios from 'axios'
import router from '../router'

const api = axios.create({
  baseURL: '/api',  // Vite proxy forwards this to http://localhost:8000/api
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// REQUEST INTERCEPTOR: attach the auth token to every request
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// RESPONSE INTERCEPTOR: handle common errors globally
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      switch (error.response.status) {
        case 401:
          // Token expired or invalid — clear it and redirect to login
          localStorage.removeItem('auth_token')
          localStorage.removeItem('auth_user')
          router.push('/login')
          break
        case 403:
          // User doesn't have permission
          alert('You do not have permission to perform this action.')
          break
        case 422:
          // Validation error — let the component handle it
          break
        case 500:
          // Server error
          console.error('Server error:', error.response.data)
          break
      }
    } else if (error.request) {
      // Network error — Laravel server might be down
      console.error('Network error: Is the Laravel server running on port 8000?')
      alert('Cannot connect to the server. Make sure Laravel is running: php artisan serve')
    }
    return Promise.reject(error)
  }
)

export default api
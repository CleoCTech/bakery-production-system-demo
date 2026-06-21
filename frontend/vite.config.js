import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import tailwindcss from '@tailwindcss/vite'
// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
    tailwindcss()
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
      },
    },
  }
})

//Without proxy setup, the frontend would not be able to make API requests to the backend due to CORS (Cross-Origin Resource Sharing) 
// restrictions. The proxy configuration allows the frontend development server to forward API requests to the backend server, 
// making it appear as if they are coming from the same origin. 
// This is essential for local development when the frontend and backend are running on different ports or domains.
//http://localhost:8000/api/products 
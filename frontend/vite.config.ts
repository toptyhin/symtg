import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  base: '/frontend/',
  server: {
    proxy: {
      '/api': {
        target: 'https://localhost',
        changeOrigin: true,
        secure: false,
      }
    }
  },
  build: {
    outDir: '../backend/public/frontend', // куда сложится js/css
    emptyOutDir: true,
    assetsDir: 'assets',
  }    
})

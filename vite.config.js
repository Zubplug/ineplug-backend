import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    host: '127.0.0.1', // ✅ Force Vite to run on this host
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000', // Laravel backend
        changeOrigin: true,
        rewrite: path => path.replace(/^\/api/, '/api'),
      },
    },
  },
});
